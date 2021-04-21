Ext.define('Rd.view.profiles.pnlTimeLimit', {
    extend      : 'Ext.panel.Panel',
    glyph       : Rd.config.icnTime,
    alias       : 'widget.pnlTimeLimit',
    requires    : [
        'Rd.view.profiles.vcTimeLimit'
    ],
    controller  : 'vcTimeLimit',
    layout      : { type: 'vbox'},
    //layout      : { type: 'vbox', align: 'center' },
    title       : "TIME LIMIT",
    initComponent: function(){
        var me      = this;
        var w_sec   = 350;
        var w_rd    = 68;
        me.width    = 550;
        me.padding  = 5;
        me.items    = [
			{
			    xtype       : 'sldrToggle',
			    fieldLabel  : 'Enabled',
			    userCls     : 'sldrDark',
			    name        : 'time_limit_enabled',
			    itemId      : 'time_limit_enabled',
			    value       : 1,
			    listeners   : {
					change  : 'sldrToggleChange'
				}
			},
			{ 
			    xtype       : 'container',
			    itemId      : 'cntDetail',
			    items       : [
			        {
                        xtype       : 'radiogroup',
                        fieldLabel  : 'Reset',
                        itemId      : 'rgrpTimeReset',
                        columns     : 2,
                        vertical    : false,
                        items       : [
                            {
                                boxLabel  : 'Daily',
                                name      : 'time_reset',
                                inputValue: 'daily',
                                margin    : '0 15 0 0',
                                checked   : true
                            }, 
                            {
                                boxLabel  : 'Weekly',
                                name      : 'time_reset',
                                inputValue: 'weekly',
                                margin    : '0 0 0 15'
                            },
                            {
                                boxLabel  : 'Monthly',
                                name      : 'time_reset',
                                inputValue: 'monthly',
                                margin    : '0 15 0 0'
                            },
                            {
                                boxLabel  : 'Never',
                                name      : 'time_reset',
                                inputValue: 'never',
                                margin    : '0 0 0 15'   
                            }
                        ]
                    },
			        {
			            xtype       : 'container',
                        layout      : 'hbox',
                        width       : w_sec+15,
                        items       : [
                            {
                                xtype       : 'displayfield',
                                width       : 180,
                                margin      : '15 0 0 15',
                                padding     : 0,
                                fieldLabel  : 'Amount',
                                value       : 1
                            },
                            {
			                    xtype       : 'sliderfield',
                                name        : 'time_amount',
                                userCls     : 'sldrDark',
                                itemId      : 'sldrTimeAmount',
                                width       : 150,
                                increment   : 1,
                                minValue    : 1,
                                maxValue    : 120,
                                listeners   : {
					                change  : 'sldrTimeAmountChange'
				                }
                            }
                        ]
                    },
                    {
                        xtype       : 'radiogroup',
                        fieldLabel  : 'Units',
                        itemId      : 'rgrpTimeUnit',
                        columns     : 3,
                        vertical    : false,
                        items       : [
                            {
                                boxLabel  : 'Minutes',
                                name      : 'time_unit',
                                inputValue: 'min',
                                margin    : '0 15 0 0',
                                checked   : true
                            }, 
                            {
                                boxLabel  : 'Hours',
                                name      : 'time_unit',
                                inputValue: 'hour',
                                margin    : '0 0 0 0'
                            },
                            {
                                boxLabel  : 'Days',
                                name      : 'time_unit',
                                inputValue: 'day',
                                margin    : '0 0 0 15'
                            }
                        ]
                    },
                    {
                        xtype       : 'radiogroup',
                        fieldLabel  : 'Type',
                        itemId      : 'rgrpTimeCap',
                        columns     : 2,
                        vertical    : false,
                        items       : [
                            {
                                boxLabel  : 'Hard',
                                name      : 'time_cap',
                                inputValue: 'hard',
                                margin    : '0 15 0 0',
                                checked   : true
                            }, 
                            {
                                boxLabel  : 'Soft',
                                name      : 'time_cap',
                                inputValue: 'soft',
                                margin    : '0 0 0 15'
                            }
                        ]
                    },
                    {
                        xtype       : 'checkbox',
                        boxLabel    : 'Apply Limit Per Device (For Click-To-Connect)',
                        name        : 'time_limit_mac',
                        margin      : '0 0 0 15'
                    }
                ]
            }
        ];       
        this.callParent(arguments);
    }
});
