Ext.define('Rd.view.dynamicDetails.gridDynamicDetailEmails' ,{
    extend      :'Ext.grid.Panel',
    alias       : 'widget.gridDynamicDetailEmails',
    multiSelect : true,
    stateful    : true,
    stateId     : 'StateGridDynamicDetailEmails',
    stateEvents :['groupclick','columnhide'],
    border      : false,
    dynamic_detail_id: undefined,
    requires: [
        'Rd.view.components.ajaxToolbar',
        'Ext.toolbar.Paging',
        'Ext.ux.ProgressBarPager'
    ],
    viewConfig: {
        loadMask:true
    },
    urlMenu: '/cake3/rd_cake/dynamic-details/menu-for-dynamic-emails.json',
    columns: [
        { text: 'DynamicDetail',  dataIndex: 'dynamic_detail_name',  hidden: true, tdCls: 'gridTree', flex: 1,stateId: 'DD_Email_A'},
        { text: 'MAC Address',    dataIndex: 'mac',      tdCls: 'gridMain', flex: 1,stateId: 'StateGridDynamicDetailEmails1'},
        { text: 'E-Mail',         dataIndex: 'email',    tdCls: 'gridTree', flex: 1,stateId: 'StateGridDynamicDetailEmails2'},
        { 
            text        : 'Captive MAC',
            dataIndex   : 'cp_mac', 
            tdCls       : 'gridTree',
            hidden      : true, 
            flex        : 1,
            stateId		: 'DD_Email_B'
        },
        { 
            text        : 'Public IP',
            dataIndex   : 'public_ip', 
            tdCls       : 'gridTree',
            hidden      : true, 
            flex        : 1,
            stateId		: 'DD_Email_C'
        },
        { 
            text        : 'NAS ID',
            dataIndex   : 'nasid', 
            tdCls       : 'gridTree',
            hidden      : true, 
            flex        : 1,
            stateId		: 'DD_Email_D'
        },
        { 
            text        : 'SSID',
            dataIndex   : 'ssid', 
            tdCls       : 'gridTree',
            hidden      : true, 
            flex        : 1,
            stateId		: 'DD_Email_E'
        },
        { 
            text        : 'Mobile Device',
            dataIndex   : 'is_mobile', 
            tdCls       : 'gridTree',
            hidden      : true, 
            flex        : 1,
            stateId		: 'DD_Email_F',
            xtype:  'templatecolumn', 
                tpl:    new Ext.XTemplate(
                    "<tpl if='is_mobile == true'><div class=\"fieldGreen\">"+i18n('sYes')+"</div></tpl>",
                    "<tpl if='is_mobile == false'><div class=\"fieldRed\">"+i18n('sNo')+"</div></tpl>"
                )
        },
        { 
            text        : 'Created',
            dataIndex   : 'created', 
            tdCls       : 'gridTree',
            hidden      : true, 
            xtype       : 'templatecolumn', 
            tpl         : new Ext.XTemplate(
                "<div class=\"fieldBlue\">{created_in_words}</div>"
            ),
            flex        : 1,
            filter      : {type: 'date',dateFormat: 'Y-m-d'},
            stateId		: 'DD_Email_G'
        },
        { 
            text        : 'Modified',
            dataIndex   : 'modified', 
            tdCls       : 'gridTree',
            hidden      : false, 
            xtype       : 'templatecolumn', 
            tpl         : new Ext.XTemplate(
                "<div class=\"fieldBlue\">{modified_in_words}</div>"
            ),
            flex        : 1,
            filter      : {type: 'date',dateFormat: 'Y-m-d'},
            stateId		: 'DD_Email_H'
        }
    ],
    initComponent: function(){
        var me      = this;
        me.tbar     = Ext.create('Rd.view.components.ajaxToolbar',{'url': me.urlMenu});

        //Create a store specific to this Dynamic Detail
        me.store = Ext.create(Ext.data.Store,{
            model       : 'Rd.model.mDataCollector',
            //To force server side sorting:
            remoteSort  : true,
            remoteFilter: true,
            proxy: {
                type    : 'ajax',
                format  : 'json',
                batchActions: true, 
                url     : '/cake3/rd_cake/data-collectors/index.json',
                extraParams: { 'dynamic_detail_id' : me.dynamic_detail_id },
                reader: {
                    type            : 'json',
                    rootProperty    : 'items',
                    messageProperty : 'message',
                    totalProperty   : 'totalCount' //Required for dynamic paging
                },
                api: {
                    destroy  : '/cake3/rd_cake/data-collectors/delete.json'
                },
                simpleSortMode: true //This will only sort on one column (sort) and a direction(dir) value ASC or DESC
            },
            autoLoad: true
        });
        
       me.bbar     =  [
            {
                 xtype       : 'pagingtoolbar',
                 store       : me.store,
                 dock        : 'bottom',
                 displayInfo : true
            }  
        ];
       
        me.callParent(arguments);
    }
});
