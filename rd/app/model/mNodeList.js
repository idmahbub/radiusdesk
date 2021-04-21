/*
 * Port to cake3: JT 09/15/18
*/
Ext.define('Rd.model.mNodeList', {
    extend: 'Ext.data.Model',
    fields: [
         {name: 'id',               type: 'int'     },
         {name: 'mesh_id',          type: 'int'     },
		 {name: 'mesh',          	type: 'string'  },
         {name: 'name',             type: 'string'  },
		 {name: 'owner',        	type: 'string'  },
         {name: 'description',      type: 'string'  },
         {name: 'mac',              type: 'string'  },
         {name: 'hardware',         type: 'string'  },
         {name: 'hw_human',         type: 'string'  },
         {name: 'power',            type: 'int'     },
         {name: 'ip',               type: 'string'  },
		 {name: 'last_contact',    	type: 'date',       dateFormat: 'Y-m-d H:i:s'   },
		 {name: 'available_to_siblings',  type: 'bool'},
         {name: 'update',       	type: 'bool'},
         {name: 'delete',       	type: 'bool'},
         {name: 'country_code',         type: 'string'  },
         {name: 'country_name',         type: 'string'  },
         {name: 'city',         type: 'string'  },
         {name: 'postal_code',         type: 'string'  },
         'last_contact_human',
         'state',
         'gateway',
         'openvpn_list',
         'config_fetched_human',
         'config_fetched',
         'config_state',
         'last_contact_from_ip',
		 'uptimhist',
		 'uptimhistpct',
		 'dayuptimehist'		
        ]
});
