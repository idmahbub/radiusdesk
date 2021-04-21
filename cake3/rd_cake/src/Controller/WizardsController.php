<?php

namespace App\Controller;
use App\Controller\AppController;

use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;

use Cake\Utility\Inflector;

class WizardsController extends AppController{

    protected $ctc              = "_Click-To-Connect";
    protected $reg              = "_User-Registration";
    protected $prof_comp_prefix = 'SimpleAdd_';
  
    public function initialize(){  
        parent::initialize(); 
        
        $this->loadComponent('Aa');
        
        $this->loadModel('Users');
        $this->loadModel('PermanentUsers');
        $this->loadModel('Realms');       
        $this->loadModel('DynamicDetails');
        $this->loadModel('DynamicPhotos');
        
        $this->loadModel('DynamicPairs');
        $this->loadModel('Radchecks');
           
        //Sept2019 -Add Simple Profiles
        $this->loadModel('Profiles');
        $this->loadModel('ProfileComponents');
        
        $this->loadModel('Radusergroups');    
        $this->loadModel('Radgroupchecks');
        $this->loadModel('Radgroupreplies');
        
        
        $this->loadModel('DynamicClients');
        $this->loadModel('DynamicClientRealms'); 
         
    }
    
    public function cancel(){
    
    
        $user = $this->Aa->user_for_token($this);
        if(!$user){   //If not a valid user
            return;
        }
        
        $user_id        = $user['id'];
        //$this->user_id  = $user_id;
        
        $this->new_name = $this->request->data['name'];
        
        if(isset($this->request->data['name'])){
            $this->new_name =  preg_replace('/^\s+/', '', $this->new_name); //Remove laeding spaces
            $this->new_name =  preg_replace('/\s+$/', '', $this->new_name); //Remove trailing spaces
        }   
        
        $this->user_id  = $this->_return_ap_id_from_name($this->new_name);
        
        //Now we have our info, now we can delete
        $this->Realms->deleteAll(['Realms.name' => $this->new_name,'Realms.user_id'=>$this->user_id]);
        $this->DynamicDetails->deleteAll(['DynamicDetails.name' => $this->new_name,'DynamicDetails.user_id'=>$this->user_id]);
        
        $this->DynamicClients->deleteAll(['DynamicClients.name' => $this->new_name,'DynamicClients.user_id'=>$this->user_id]);
        
        //Delete the profiles and their linked profile components
        $e_pc_basic = $this->Profiles->find()->where(['Profiles.name' => $this->new_name,'Profiles.user_id'=>$this->user_id])->first();
        if($e_pc_basic){
            $pc_name = $this->prof_comp_prefix.$e_pc_basic->id;
            $this->ProfileComponents->deleteAll(['ProfileComponents.name' => $pc_name,'ProfileComponents.user_id'=>$this->user_id]);
            $this->Profiles->deleteAll(['Profiles.name' => $this->new_name,'Profiles.user_id'=>$this->user_id]);
        }
        
        $e_pc_c_t_c = $this->Profiles->find()->where(['Profiles.name' => $this->new_name.$this->ctc,'Profiles.user_id'=>$this->user_id])->first();
        if($e_pc_c_t_c){
            $pc_name = $this->prof_comp_prefix.$e_pc_c_t_c->id;
            $this->ProfileComponents->deleteAll(['ProfileComponents.name' => $pc_name,'ProfileComponents.user_id'=>$this->user_id]);
            $this->Profiles->deleteAll(['Profiles.name' => $this->new_name.$this->ctc,'Profiles.user_id'=>$this->user_id]);
            $this->{'Radusergroups'}->deleteAll(['Radusergroups.username' => $this->new_name.$this->ctc, 'Radusergroups.groupname' => $pc_name]);
            $this->{'Radgroupreplies'}->deleteAll(['groupname' => $pc_name]);
            $this->{'Radgroupchecks'}->deleteAll(['groupname' => $pc_name]);
        }
        
        $e_pc_reg = $this->Profiles->find()->where(['Profiles.name' => $this->new_name.$this->reg,'Profiles.user_id'=>$this->user_id])->first();
        if($e_pc_reg){
            $pc_name = $this->prof_comp_prefix.$e_pc_reg->id;
            $this->ProfileComponents->deleteAll(['ProfileComponents.name' => $pc_name,'ProfileComponents.user_id'=>$this->user_id]);
            $this->Profiles->deleteAll(['Profiles.name' => $this->new_name.$this->reg,'Profiles.user_id'=>$this->user_id]);
            $this->{'Radusergroups'}->deleteAll(['Radusergroups.username' => $this->new_name.$this->reg, 'Radusergroups.groupname' => $pc_name]);
            $this->{'Radgroupreplies'}->deleteAll(['groupname' => $pc_name]);
            $this->{'Radgroupchecks'}->deleteAll(['groupname' => $pc_name]);
        }
        
  
        //Access Provder 
        $ap_name = preg_replace('/\s+/', '_', $this->new_name);
        $ap_name = strtolower($ap_name);
        
        //Delete the sample file
        $dest  = WWW_ROOT."img/dynamic_photos/".$ap_name.".jpg";
        unlink($dest);
        
        $this->DynamicClients->deleteAll(
            ['DynamicClients.nasidentifier LIKE ' => $ap_name.'_mcp_%','DynamicClients.user_id'=>$this->user_id]);
        
        $this->PermanentUsers->deleteAll(
            ['PermanentUsers.username' => $ap_name.'@'.$ap_name,'PermanentUsers.user_id'=>$this->user_id]);
        
        $this->Radchecks->deleteAll(['Radchecks.username' => $ap_name.'@'.$ap_name]);
        
        $this->PermanentUsers->deleteAll(['PermanentUsers.username' => 'click_to_connect@'.$ap_name,'PermanentUsers.user_id'=>$this->user_id]);
        
        $this->Radchecks->deleteAll(['Radchecks.username' => 'click_to_connect@'.$ap_name]);
        
        $this->Users->deleteAll(['Users.username' => $ap_name,'Users.id'=>$this->user_id]);
      
        $this->set(array(
            'items'     => array(),
            'success'   => true,
            '_serialize' => array('items','success')
        ));
    }
    
    public function newSiteStepOne(){
	
	    $user = $this->Aa->user_for_token($this);
        if(!$user){   //If not a valid user
            return;
        }
        
        $user_id        = $user['id'];
        $this->user_id  = $user_id;
        $this->new_name = $this->request->data['name'];
        
        if(isset($this->request->data['name'])){
            $this->new_name =  preg_replace('/^\s+/', '', $this->new_name); //Remove leading spaces
            $this->new_name =  preg_replace('/\s+$/', '', $this->new_name); //Remove trailing spaces
        }
        
        $this->pwd      = $this->request->data['password'];
             
        $exist_test = $this->_test_if_exists($this->new_name);
        
        if(!$exist_test){
            $this->_add_items($this->new_name);
        }else{
            $this->set(array(
                'errors'    => array('name' => "Items with this name alreay exist: $exist_test"),
                'success'   => false,
                'message'   => array($exist_test." already exist"),
                '_serialize' => array('errors','success','message')
            ));
            return;
        }
        	
        $this->set(array(
            'items'     => array(),
            'success'   => true,
            '_serialize' => array('items','success')
        ));
    }
    
    public function changeTheme(){
    
         $user = $this->Aa->user_for_token($this);
        if(!$user){   //If not a valid user
            return;
        }
        $this->new_name = $this->request->data['name'];
        
        if(isset($this->request->data['name'])){
            $this->new_name =  preg_replace('/^\s+/', '', $this->new_name); //Remove laeding spaces
            $this->new_name =  preg_replace('/\s+$/', '', $this->new_name); //Remove trailing spaces
        }
        
        $this->user_id  = $this->_return_ap_id_from_name($this->new_name);
        
        $e = $this->DynamicDetails->find()
            ->where(
                [
                    'DynamicDetails.name'        => $this->new_name,
                    'DynamicDetails.user_id'     => $this->user_id,
                ]
            )
            ->first();
        
        if($e){
            $e->theme = $this->request->data['theme'];
            $this->DynamicDetails->save($e);  
        }
    
        $this->set(array(
            'data'      => array(),
            'success'   => true,
            '_serialize'=> array('data','success')
        ));
    }
    
    public function newSiteStepTwo(){
       
        $user = $this->Aa->user_for_token($this);
        if(!$user){   //If not a valid user
            return;
        }
        
        $user_id        = $user['id'];
        //$this->user_id  = $user_id;
        $this->new_name = $this->request->data['name'];
        
         if(isset($this->request->data['name'])){
            $this->new_name =  preg_replace('/^\s+/', '', $this->new_name); //Remove laeding spaces
            $this->new_name =  preg_replace('/\s+$/', '', $this->new_name); //Remove trailing spaces
        }
        
        
        $this->user_id  = $this->_return_ap_id_from_name($this->new_name);
        
        $q_r = $this->DynamicDetails->find()
            ->where([
                'DynamicDetails.name'        => $this->new_name,
                'DynamicDetails.user_id'     => $this->user_id,
            ])
            ->first();
        if($q_r){
            $id = $q_r->id;
            $d = array();
            $d['id'] = $id;
            $check_items = array('voucher_login_check','user_login_check','eth_br_for_all', 'connect_check');
            foreach($check_items as $i){
                if(isset($this->request->data[$i])){
                    $d[$i] = 1;
                }else{
                    $d[$i] = 0;
                }
            }
            $this->DynamicDetails->patchEntity($q_r, $d);
            $this->DynamicDetails->save($q_r);  
        }
            
        $this->set(array(
            'items'     => array(),
            'success'   => true,
            '_serialize' => array('items','success')
        ));   
    }
    
     public function viewLogo(){
        $user = $this->Aa->user_for_token($this);
        if(!$user){   //If not a valid user
            return;
        }   
        $user_id        = $user['id'];
        //$this->user_id  = $user_id;
        $this->new_name = $this->request->query['name'];
        $this->user_id  = $this->_return_ap_id_from_name($this->new_name);
        $q_r            = $this->DynamicDetails->find()
            ->where(['DynamicDetails.name' => $this->new_name,'DynamicDetails.user_id' => $this->user_id])
            ->first();
        if($q_r){
            $this->set(array(
                'data'     => $q_r->toArray(),
                'success'   => true,
                '_serialize'=> array('success', 'data')
            ));
        }else{
            $this->set(array(
                'success'   => false,
                '_serialize'=> array('success')
            ));
        }
    }
    
    public function uploadLogo($id = null){

        //__ Authentication + Authorization __
        $user = $this->Aa->user_for_token($this);
        if(!$user){   //If not a valid user
            return;
        }   

        $this->viewBuilder()->layout('ext_file_upload');

        $path_parts     = pathinfo($_FILES['photo']['name']);
        $unique         = time();
        $dest           = WWW_ROOT."img/dynamic_details/".$unique.'.'.$path_parts['extension'];
        $dest_realm     = WWW_ROOT."img/realms/".$unique.'.'.$path_parts['extension'];
        $dest_www       = "/cake3/rd_cake/webroot/img/dynamic_details/".$unique.'.'.$path_parts['extension'];

        //Now add....
        $data['photo_file_name']  = $unique.'.'.$path_parts['extension'];
        
        $user_id        = $user['id'];
        //$this->user_id  = $user_id;
        $this->new_name = $this->request->data['name'];
        
        if(isset($this->request->data['name'])){
            $this->new_name =  preg_replace('/^\s+/', '', $this->new_name); //Remove laeding spaces
            $this->new_name =  preg_replace('/\s+$/', '', $this->new_name); //Remove trailing spaces
        }
        
        $this->user_id  = $this->_return_ap_id_from_name($this->new_name);
        $q_r            = $this->DynamicDetails->find()
            ->where(['DynamicDetails.name' => $this->new_name,'DynamicDetails.user_id' => $this->user_id])
            ->first();
              
        if($q_r){ 
            $old_file       = $q_r->icon_file_name; 
            $q_r->icon_file_name = $unique.'.'.$path_parts['extension']; 
            if($this->DynamicDetails->save($q_r)){
                move_uploaded_file ($_FILES['photo']['tmp_name'] , $dest);
                //Remove old files
                $file_to_delete     = WWW_ROOT."img/dynamic_details/".$old_file;
                $file_to_delete_r   = WWW_ROOT."img/realms/".$old_file;
                if(file_exists($file_to_delete)){
                    unlink($file_to_delete);
                }
                if(file_exists($file_to_delete_r)){
                    unlink($file_to_delete_r);
                }
                
                //----------------------------
                //Also take care of the realm 
                copy($dest,$dest_realm);
                $q_realm = $this->Realms->find()
                    ->where(['Realms.name' => $this->new_name,'Realms.user_id' => $this->user_id])
                    ->first();
                if($q_realm){
                    $q_realm->icon_file_name = $unique.'.'.$path_parts['extension'];
                    $this->Realms->save($q_realm);
                }
                //----------------------------            
                $json_return['id']                  = $q_r->id;
                $json_return['success']             = true;
                $json_return['icon_file_name']      = $unique.'.'.$path_parts['extension'];
            }else{
                $errors = $q_r->errors();
                $a = [];
                foreach(array_keys($errors) as $field){
                    $detail_string = '';
                    $error_detail =  $errors[$field];
                    foreach(array_keys($error_detail) as $error){
                        $detail_string = $detail_string." ".$error_detail[$error];   
                    }
                    $a[$field] = $detail_string;
                }
                      
                $json_return['errors']      = $a;
                $json_return['message']     = __('Problem uploading Logo');
                $json_return['success']     = false;
            }  
        }else{
            $errors = $q_r->errors();
            $a = [];
            foreach(array_keys($errors) as $field){
                $detail_string = '';
                $error_detail =  $errors[$field];
                foreach(array_keys($error_detail) as $error){
                    $detail_string = $detail_string." ".$error_detail[$error];   
                }
                $a[$field] = $detail_string;
            }       
            $json_return['errors']      = $a;
            $json_return['message']     = __('Problem uploading Logo');
            $json_return['success']     = false;
        }
        $this->set('json_return',$json_return);
    }
    
     public function indexPhoto(){
    
        $user = $this->Aa->user_for_token($this);
        if(!$user){   //If not a valid user
            return;
        }   
        $user_id        = $user['id'];
        //$this->user_id  = $user_id;
        $this->new_name = $this->request->query['name'];
        $this->user_id  = $this->_return_ap_id_from_name($this->new_name);
        
        $q_r            = $this->DynamicDetails->find()
            ->where(['DynamicDetails.name' => $this->new_name,'DynamicDetails.user_id' => $this->user_id])
            ->first();
        
        if($q_r){
        
            $id = $q_r->id;
            $items = array();
        
            $q_p = $this->DynamicPhotos->find()
                ->where(['DynamicPhotos.dynamic_detail_id' => $id])
                ->all();
                
            $fields = $this->{'DynamicPhotos'}->schema()->columns();
   
            foreach($q_p as $p){
            
           	    $row = array();
                foreach($fields as $field){
                    $row["$field"]= $p->{"$field"};
                }
                
                $photo = Configure::read('paths.real_photo_path').$p->file_name;
                $row['img']= "/cake3/rd_cake/webroot/files/image.php?width=400&height=200&image=".$photo;      
           	    array_push($items,$row);
            }
       
            $this->set(array(
                'items'     => $items,
                'success'   => true,
                '_serialize'=> array('success', 'items')
            ));
        }else{
            $this->set(array(
                'success'   => false,
                '_serialize'=> array('success')
            ));
        }
    }
    
    public function uploadPhoto(){

        //__ Authentication + Authorization __
        $user = $this->Aa->user_for_token($this);
        if(!$user){   //If not a valid user
            return;
        }
         
        $this->viewBuilder()->layout('ext_file_upload');  
        
        //We find the ID for this name....
        $user_id        = $user['id'];
        //$this->user_id  = $user_id;
        $this->new_name = $this->request->data['name'];
        
        if(isset($this->request->data['name'])){
            $this->new_name =  preg_replace('/^\s+/', '', $this->new_name); //Remove laeding spaces
            $this->new_name =  preg_replace('/\s+$/', '', $this->new_name); //Remove trailing spaces
        }
        
        
        $this->user_id  = $this->_return_ap_id_from_name($this->new_name);
        $q_r            = $this->DynamicDetails->find()
            ->where(['DynamicDetails.name' => $this->new_name,'DynamicDetails.user_id' => $this->user_id])
            ->first();
       
        $check_items = array('active','include_title','include_description');
        foreach($check_items as $ci){
            if(isset($this->request->data[$ci])){
                $this->request->data[$ci] = 1;
            }else{
                $this->request->data[$ci] = 0;
            }
        }
        
        $path_parts     = pathinfo($_FILES['photo']['name']);
        $unique         = time();
        $dest           =  WWW_ROOT."img/dynamic_photos/".$unique.'.'.$path_parts['extension'];
        $dest_www       = "/cake3/rd_cake/webroot/img/dynamic_photos/".$unique.'.'.$path_parts['extension'];

        $this->request->data['dynamic_detail_id']   = $q_r->id;
        $this->request->data['file_name']           = $unique.'.'.$path_parts['extension'];
        
        $entity = $this->DynamicPhotos->newEntity($this->request->data()); 
        if($this->DynamicPhotos->save($entity)){
            move_uploaded_file ($_FILES['photo']['tmp_name'] , $dest);
            $json_return['id']                  = $entity->id;
            $json_return['success']             = true;

        }else{
            $message = 'Error';
            $errors = $entity->errors();
            $a = [];
            foreach(array_keys($errors) as $field){
                $detail_string = '';
                $error_detail =  $errors[$field];
                foreach(array_keys($error_detail) as $error){
                    $detail_string = $detail_string." ".$error_detail[$error];   
                }
                $a[$field] = $detail_string;
            } 
            
            $json_return['errors']      = $a;
            $json_return['message']     = array("message"   => __('Problem uploading photo'));
            $json_return['success']     = false;
        }
        $this->set('json_return',$json_return);
    }
    
    public function deletePhoto() {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}

        $user = $this->Aa->user_for_token($this);
        if(!$user){   //If not a valid user
            return;
        }   

	    if(isset($this->request->data['id'])){   //Single item delete
            //Get the filename to delete
            $entity = $this->DynamicPhotos->get($this->request->data['id']);
            if($entity){
                $file_to_delete = WWW_ROOT."img/dynamic_photos/".$entity->file_name;
                if($this->DynamicPhotos->delete($entity)){
                    if(file_exists($file_to_delete)){
                        unlink($file_to_delete);
                    }
                }
            }     
            
        }else{                          //Assume multiple item delete
            foreach($this->request->data as $d){
                //Get the filename to delete
                $entity = $this->DynamicPhotos->get($d['id']);
                if($entity){
                    $file_to_delete = WWW_ROOT."img/dynamic_photos/".$entity->file_name;
                    if($this->DynamicPhotos->delete($entity)){
                        if(file_exists($file_to_delete)){
                            unlink($file_to_delete);
                        }
                    }
                }
            }
        }

        $this->set(array(
            'success' => true,
            '_serialize' => array('success')
        ));
	}
    
    private function _add_items($name){
    
         //Access Provder 
        $ap_name        = preg_replace('/\s+/', '_', $this->new_name);
        $ap_name        = strtolower($ap_name);
        $this->ap_name  = $ap_name; //Make it global accessable
        
        
        //We start with a sub-provider 
        $d_user                 = array();
        $d_user['username']     = $ap_name;
        $d_user['password']     = $this->pwd;
        $d_user['parent_id']    = $this->user_id;
        $d_user['language_id']  = 4; //*SPECIAL
        $d_user['country_id']   = 4; //*SPECIAL
        $d_user['group_id']     = 9; //*SPECIAL
        $d_user['monitor']      = 1;
        $d_user['active']       = 1; 
        $d_user['token']        = ''; 
        
        $e_user         = $this->{'Users'}->newEntity($d_user);
        $this->{'Users'}->save($e_user);
        $ap_id          =  $e_user->id;
        $this->ap_id    = $ap_id;
        
           
        //return; 
        $d_common = [];
        $d_common['user_id']                = $this->ap_id;
        $d_common['name']                   = $name;
        $d_common['available_to_siblings']  = 1;
        
        $this->d_common = $d_common; 
        
        $d_common['suffix']                 = $this->ap_name; 
        $d_common['suffix_permanent_users'] = true;
        $d_common['suffix_vouchers']        = false;        //ONLY for the permanent users      
    
        $e_realm = $this->{'Realms'}->newEntity($d_common);
        $this->{'Realms'}->save($e_realm);
        $realm_id =  $e_realm->id;
          
        //|42|32|NULL|NULL|View users or vouchers not created self| 
        $this->Acl->allow(array('model' => 'Users', 'foreign_key' => $ap_id),$this->_return_aco_path(42));
      
      /* 17 September 2019 With the simplified profiles we can actually allow this again
        //|167|166|NULL|NULL |index|     //Profiles
        $this->Acl->deny(array('model' => 'Users', 'foreign_key' => $ap_id),$this->_return_aco_path(167)); 
        //|259|258|NULL|NULL |index|     //Profile Components
        $this->Acl->deny(array('model' => 'Users', 'foreign_key' => $ap_id),$this->_return_aco_path(259));
        
        //Also now we create specific profiles for the Access Provider (making it available to sub-providers)
      
      */
      
        //===========
        //BASIC
        $d_profile_basic = [];
        $d_profile_basic['name']    = $name;
        $d_profile_basic['user_id'] = $this->ap_id;
        $d_profile_basic['available_to_siblings'] = 0;
        $e_profile_basic = $this->{'Profiles'}->newEntity($d_profile_basic);
        $this->{'Profiles'}->save($e_profile_basic);
        
        $d_pc_basic                             = [];
        $d_pc_basic['user_id']                  = $this->ap_id;
        $d_pc_basic['available_to_siblings']    = 0;
        $d_pc_basic['name']                     = $this->prof_comp_prefix.$e_profile_basic->id;
        $e_pc_basic = $this->{'ProfileComponents'}->newEntity($d_pc_basic);
        $this->{'ProfileComponents'}->save($e_pc_basic);
        
        //We have to add this in order to 'bind' the profile to the user ... even if its empty
        $d_rad_ug               = [];
        $d_rad_ug['username']   = $name;
        $d_rad_ug['groupname']  = $this->prof_comp_prefix.$e_profile_basic->id;
        $d_rad_ug['priority']   = 5;
        
        $e_rad_ug = $this->{'Radusergroups'}->newEntity($d_rad_ug);
        $this->{'Radusergroups'}->save($e_rad_ug);
        
        
        //CLICK TO CONNECT
        $d_profile_c_t_c = [];
        $d_profile_c_t_c['name']    = $name.$this->ctc;
        $d_profile_c_t_c['user_id'] = $this->ap_id;
        $d_profile_c_t_c['available_to_siblings'] = 0;
        $e_profile_c_t_c = $this->{'Profiles'}->newEntity($d_profile_c_t_c);
        $this->{'Profiles'}->save($e_profile_c_t_c);
        
        $d_pc_c_t_c                             = [];
        $d_pc_c_t_c['user_id']                  = $this->ap_id;
        $d_pc_c_t_c['available_to_siblings']    = 0;
        $d_pc_c_t_c['name']                     = $this->prof_comp_prefix.$e_profile_c_t_c->id;
        $e_pc_c_t_c = $this->{'ProfileComponents'}->newEntity($d_pc_c_t_c);
        $this->{'ProfileComponents'}->save($e_pc_c_t_c);
        
        $d_rad_ug               = [];
        $d_rad_ug['username']   = $name.$this->ctc;
        $d_rad_ug['groupname']  = $this->prof_comp_prefix.$e_profile_c_t_c->id;
        $d_rad_ug['priority']   = 5;
        
        $e_rad_ug = $this->{'Radusergroups'}->newEntity($d_rad_ug);
        $this->{'Radusergroups'}->save($e_rad_ug);
        
        $d_reset = [
            'groupname' => $this->prof_comp_prefix.$e_profile_c_t_c->id,
            'attribute' => 'Rd-Reset-Type-Data',
            'op'        => ':=',
            'value'     => 'daily',
            'comment'   => 'SimpleProfile'
        ];
        $e_data_reset = $this->{'Radgroupchecks'}->newEntity($d_reset);
        $this->{'Radgroupchecks'}->save($e_data_reset);
    
        $d_amount = [
            'groupname' => $this->prof_comp_prefix.$e_profile_c_t_c->id,
            'attribute' => 'Rd-Total-Data',
            'op'        => ':=',
            'value'     => '250000000',
            'comment'   => 'SimpleProfile'
        ];
        $e_data_amount = $this->{'Radgroupchecks'}->newEntity($d_amount);
        $this->{'Radgroupchecks'}->save($e_data_amount);
        
        $d_cap = [
            'groupname' => $this->prof_comp_prefix.$e_profile_c_t_c->id,
            'attribute' => 'Rd-Cap-Type-Data',
            'op'        => ':=',
            'value'     => 'hard',
            'comment'   => 'SimpleProfile'
        ];
        $e_data_cap = $this->{'Radgroupchecks'}->newEntity($d_cap);
        $this->{'Radgroupchecks'}->save($e_data_cap);
       
        $d_mac = [
            'groupname' => $this->prof_comp_prefix.$e_profile_c_t_c->id,
            'attribute' => 'Rd-Mac-Counter-Data',
            'op'        => ':=',
            'value'     => '1',
            'comment'   => 'SimpleProfile'
        ];
        $e_data_mac = $this->{'Radgroupchecks'}->newEntity($d_mac);
        $this->{'Radgroupchecks'}->save($e_data_mac);
        
        $d_up = [
            'groupname' => $this->prof_comp_prefix.$e_profile_c_t_c->id,
            'attribute' => 'WISPr-Bandwidth-Max-Up',
            'op'        => ':=',
            'value'     => 512000,
            'comment'   => 'SimpleProfile'
        ];
        
        $e_up = $this->{'Radgroupreplies'}->newEntity($d_up);
        $this->{'Radgroupreplies'}->save($e_up);
        
        $d_down = [
            'groupname' => $this->prof_comp_prefix.$e_profile_c_t_c->id,
            'attribute' => 'WISPr-Bandwidth-Max-Down',
            'op'        => ':=',
            'value'     => 512000,
            'comment'   => 'SimpleProfile'
        ];
        
        $e_down = $this->{'Radgroupreplies'}->newEntity($d_down);
        $this->{'Radgroupreplies'}->save($e_down);
        
        
        //USER REGISTRATION
        $d_profile_reg = [];
        $d_profile_reg['name']    = $name.$this->reg;
        $d_profile_reg['user_id'] = $this->ap_id;
        $d_profile_reg['available_to_siblings'] = 0;
        $e_profile_reg = $this->{'Profiles'}->newEntity($d_profile_reg);
        $this->{'Profiles'}->save($e_profile_reg);
        
        $d_pc_reg                             = [];
        $d_pc_reg['user_id']                  = $this->ap_id;
        $d_pc_reg['available_to_siblings']    = 0;
        $d_pc_reg['name']                     = $this->prof_comp_prefix.$e_profile_reg->id;
        $e_pc_reg = $this->{'ProfileComponents'}->newEntity($d_pc_reg);
        $this->{'ProfileComponents'}->save($e_pc_reg);     
        
        $d_rad_ug               = [];
        $d_rad_ug['username']   = $name.$this->reg;
        $d_rad_ug['groupname']  = $this->prof_comp_prefix.$e_profile_reg->id;
        $d_rad_ug['priority']   = 5;
        
        $e_rad_ug_reg = $this->{'Radusergroups'}->newEntity($d_rad_ug);
        $this->{'Radusergroups'}->save($e_rad_ug_reg);
            
        
        //===========
      
        //Allow CRUD for this Access Provider for this realm  
        $this->Acl->allow(
                array('model' => 'Users', 'foreign_key' => $ap_id), 
                array('model' => 'Realms','foreign_key' => $realm_id));             
        //Get the ID for the Sample Permanent User's profile 

        Configure::load('Wizards','default');
        /*
        $pu_profile = Configure::read('Wizards.new_site.pu_profile'); //Read the defaults
        $q_r        = $this->Profiles->find()->where(['Profiles.name' => $pu_profile])->first();
        $profile_id = '';
        if($q_r){
            $profile_id = $q_r->id;
        }
        */
        
        $d_user['profile']    = $e_profile_basic->name;
        $d_user['profile_id'] = $e_profile_basic->id;
        
        $d_user['realm']      = $this->new_name;
        $d_user['realm_id']   = $realm_id;
        
        $d_user['country_id'] = 4;
        $d_user['language_id']= 4; 
        $d_user['user_id']    = $this->ap_id;
        
        //Make the username so that it has the suffix
        $d_user['username']    = $ap_name.'@'.$ap_name;
         
        $e_pu = $this->PermanentUsers->newEntity($d_user);       
        $this->PermanentUsers->save($e_pu);
        $permanent_user_id =  $e_pu->id;
            
        $d_detail = $d_common;
         
        //===Auto Suffix==
        $d_detail['auto_suffix_check']      = 1;
        $d_detail['auto_suffix']            = $ap_name;  
        
        //==Registration==
        $d_detail['realm_id']   = $realm_id;
        /*
        $profile_register       = Configure::read('Wizards.new_site.profile_register');  
        $q_r                    = $this->Profiles->find()->where(['Profiles.name' => $profile_register])->first();
        $profile_register_id    = '';
        if($q_r){
            $profile_register_id = $q_r->id;
        }
        */
        
        $d_detail['profile_id']             = $e_profile_reg->id;

        $d_detail['reg_auto_suffix_check']  = 1;
        $d_detail['reg_auto_suffix']        = $ap_name;
        
        //Click to Connect
        $d_c_t_c                = array();
        $d_c_t_c['username']    = 'click_to_connect@'.$ap_name;
        $d_c_t_c['password']    = 'click_to_connect';
        $d_c_t_c['language_id'] = '4'; //*SPECIAL
        $d_c_t_c['active']      = 1;
        $d_c_t_c['country_id']  = 4;
        $d_c_t_c['user_id']     = $this->ap_id; 
        $d_c_t_c['realm']       = $this->new_name;
        $d_c_t_c['realm_id']    = $realm_id;
        
        //Get the ID for the Sample Permanent User's profile 
        Configure::load('Wizards','default'); 
        /*
        $c_t_profile            = Configure::read('Wizards.new_site.c_t_c_profile'); //Read the defaults
        $q_r                    = $this->Profiles->find()->where(['Profiles.name' => $c_t_profile])->first();
        $c_t_c_profile_id       = '';
        if($q_r){
            $c_t_c_profile_id = $q_r->id;
        }
        */
        
        $d_c_t_c['profile']     = $e_profile_c_t_c->name;
        $d_c_t_c['profile_id']  = $e_profile_c_t_c->id; 
        $e_c_t_c                = $this->PermanentUsers->newEntity($d_c_t_c);   
        $this->PermanentUsers->save($e_c_t_c);
        $c_t_c_user_id          =  $e_c_t_c->id; 
     
        $d_detail['connect_check']          = 1;       
        $d_detail['connect_username']       = 'click_to_connect';
        $d_detail['connect_suffix']         = 'ssid'; 
             
        $e_d_detail = $this->DynamicDetails->newEntity($d_detail); 
        $this->DynamicDetails->save($e_d_detail);
        $dynamic_detail_id = $e_d_detail->id;
        
        //Add a sample background
        $source     = WWW_ROOT."img/dynamic_photos/sample.jpg";
        $dest       = WWW_ROOT."img/dynamic_photos/".$ap_name.".jpg";
        copy($source, $dest);
        
        $d_photo                        = array();
        $d_photo['dynamic_detail_id']   = $dynamic_detail_id;
        $d_photo['file_name']           = $ap_name.".jpg";
        $d_photo['title']               = "Sample Title";
        $d_photo['description']         = "Sample Description";
        
        $e_d_photo = $this->DynamicPhotos->newEntity($d_photo);
        $this->DynamicPhotos->save($e_d_photo);
        
        $e_d_client = $this->DynamicClients->newEntity($d_common);
        $this->DynamicClients->save($e_d_client);
        $dynamic_client_id = $e_d_client->id;
        
        $d_dc_r = array();
        $d_dc_r['dynamic_client_id'] = $dynamic_client_id;
        $d_dc_r['realm_id'] = $realm_id;
        
        $e_d_dc_r = $this->DynamicClientRealms->newEntity($d_dc_r);
        $this->DynamicClientRealms->save($e_d_dc_r);
        $dynamic_client_realm_id = $e_d_dc_r->id;
    }
    
     private function _complete_ap_profile($ap_profile_id,$realm_id,$dynamic_detail_id){
        //Entry point Guest
        $d_ap_guest = array();
        $d_ap_guest['ap_profile_id']    = $ap_profile_id;
        $d_ap_guest['name']             = $this->ssid_guest;
        $d_ap_guest['isolate']          = 1;
        
        $e_ap_guest = $this->ApProfileEntries->newEntity($d_ap_guest);
        $this->ApProfileEntries->save($e_ap_guest);
        $entry_guest_id = $e_ap_guest->id;
        
        //Entry point Wireless
        $d_ap_wireless = array();
        $d_ap_wireless['ap_profile_id']    = $ap_profile_id;
        $d_ap_wireless['name']             = $this->ssid_wireless;
        $d_ap_wireless['encryption']       = 'psk2';
        $d_ap_wireless['special_key']      = $this->key_wireless;
        
        $e_ap_wireless = $this->ApProfileEntries->newEntity($d_ap_wireless);
        $this->ApProfileEntries->save($e_ap_wireless);
        $entry_wireless_id = $e_ap_wireless->id;
        
        //Exit point Guest
        $d_exit_guest                           = array();
        $d_exit_guest['type']                   = 'captive_portal';
        $d_exit_guest['ap_profile_id']          = $ap_profile_id;
        $d_exit_guest['auto_dynamic_client']    = 1;
        $d_exit_guest['auto_login_page']        = 1;
        $d_exit_guest['realm_list']             = $realm_id; //**
        $d_exit_guest['dynamic_detail_id']      = $dynamic_detail_id; //**
        
        $e_exit_guest   = $this->ApProfileExits->newEntity($d_exit_guest);
        $this->ApProfileExits->save($e_exit_guest);
        $exit_guest_id  = $e_exit_guest->id;
        
        //Exit point Wireless
        $d_exit_wireless                        = array();
        $d_exit_wireless['type']                = 'bridge';
        $d_exit_wireless['ap_profile_id']       = $ap_profile_id;
        
        $e_exit_wireless    = $this->ApProfileExits->newEntity($d_exit_wireless);   
        $this->ApProfileExits->save($e_exit_wireless);
        $exit_wireless_id   = $e_exit_wireless->id;
        
        //Now marry them.....
        $d_ap_m_guest = array();
        $d_ap_m_guest['ap_profile_exit_id']     = $exit_guest_id;
        $d_ap_m_guest['ap_profile_entry_id']    = $entry_guest_id;
        
        $e_ap_m_guest    = $this->ApProfileExitApProfileEntries->newEntity($d_ap_m_guest);
        $this->ApProfileExitApProfileEntries->save($e_ap_m_guest);
        
        $d_ap_m_wireless = array();
        $d_ap_m_wireless['ap_profile_exit_id']     = $exit_wireless_id;
        $d_ap_m_wireless['ap_profile_entry_id']    = $entry_wireless_id;
        
        $e_ap_m_wireless    = $this->ApProfileExitApProfileEntries->newEntity($d_ap_m_wireless);  
        $this->ApProfileExitApProfileEntries->save($e_ap_m_wireless);
        
        //Add an entry for the captive portal
        Configure::load('ApProfiles','default'); 
        $d_cp = Configure::read('ApProfiles.captive_portal'); //Read the defaults
        $d_cp['coova_optional']     = "ssid ".$this->ap_name."\n";
        $d_cp['ap_profile_exit_id'] = $exit_guest_id;
        $e_cp = $this->ApProfileExitCaptivePortals->newEntity($d_cp);
        $this->ApProfileExitCaptivePortals->save($e_cp);
    }
     
    private function _test_if_exists($name){
    
        $tables_to_check = [
            'Realms'            => 'Realm',
            'DynamicDetails'    => 'Dynamic Login Page',
            'DynamicClients'    => 'Dynamic RADIUS Client',
            'Profiles'          => 'Profile'
        ];
        
        foreach(array_keys($tables_to_check) as $key){
            $count = $this->{$key}->find()->where(['name' => $name])->count();
            if($count > 0){
                return $tables_to_check[$key];
            }
        }
           
        //Access Provder 
        $ap_name = preg_replace('/\s+/', '_', $this->new_name);
        $ap_name = strtolower($ap_name);
        
        if($this->{'PermanentUsers'}->find()->where(['PermanentUsers.username' => $ap_name.'@'.$ap_name])->count() > 0){  
            return 'Permanent User';
        }
        
        if($this->{'PermanentUsers'}->find()->where(['PermanentUsers.username' => 'click_to_connect@'.$ap_name])->count() > 0){
            return 'Permanent User';
        }
        
        if($this->{'Users'}->find()->where(['Users.username' => $ap_name])->count() > 0){  
            return "Access Provider";
        }
        return false;
    }
    
    private function _return_aco_path($id){
    
        $parents        = $this->Acl->Aco->find('path', ['for' => $id]); 
        $path_string    = '';
        foreach($parents as $line_num => $i){
            if($line_num == 0){
                $path_string = $i->alias;
            }else{
                $path_string = $path_string."/".$i->alias;
            }
        }
        return $path_string;
    }
    
    private function _make_linux_password($pwd){
		return exec("openssl passwd -1 $pwd");
	}
    
    private function _return_ap_id_from_name($name){	
	    $ap_name        = preg_replace('/\s+/', '_',$name);
        $ap_name        = strtolower($ap_name);
        $qr             = $this->Users->find()->where(['Users.username' => $ap_name])->first();      
        $id = false; 
        if($qr){
            $id = $qr->id;
        }
	    return $id;
	}
	
}
