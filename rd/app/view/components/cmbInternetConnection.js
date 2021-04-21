Ext.define('Rd.view.components.cmbInternetConnection', {
    extend          : 'Ext.form.ComboBox',
    alias           : 'widget.cmbInternetConnection',
    fieldLabel      : 'Internet Connect',
    labelSeparator  : '',
    forceSelection  : true,
    queryMode       : 'local',
    displayField    : 'text',
    valueField      : 'id',
    typeAhead       : true,
    allowBlank      : false,
    mode            : 'local',
    name            : 'internet_connection',
    labelClsExtra   : 'lblRd',
    value           : 'auto_detect',
    initComponent   : function() {
        var me= this;
        var s = Ext.create('Ext.data.Store', {
            fields: ['id', 'text'],
            data : [
                {"id":"auto_detect", "text": 'Auto Detect'},
                {"id":"wifi",        "text": 'WIFI Client'}
            ]
        });
        me.store = s;
        this.callParent(arguments);
    }
});
