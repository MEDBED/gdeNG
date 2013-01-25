Ext.ux.QueryBuilderFormWindow = function(config){
	Ext.apply( this, config );
	
	var buttons = ['->', {
		text: 'Save',
		icon : this.filePath+'images/icons/save.png',
		handler: function(){
			this.saveFormDetails(true);
		}, 
		scope: this	
	},{
		text: 'Cancel',
		icon : this.filePath+'images/icons/cancel.png',
		handler: function(){
			this.hide();
		}, 
		scope: this
	}];
	
	this.formPanel = new Ext.FormPanel({
		waitMsgTarget: true,
		url: config.querySaveUrl,
		baseParams: {},
		fileUpload: true,
		border: false,	
		layout:'fit',
		items: [{
			border:true,
			bbar: buttons,
			autoScroll:this.autoScroll?true:false,
			bodyStyle: 'background-color:#F5F5F5;',
			defaults:{bodyStyle: 'background-color:#F5F5F5;', defaults:{bodyStyle: 'padding:5px;background-color:#F5F5F5;'}},
			items:this.formDetails
		}]
	});	
	
	this.labelWidth 	= 100;	
	this.defaults		= {border: false};
	this.deferredRender	= false;
	this.border			= false;
	
	this.items			= [ this.formPanel ];	
	
	Ext.ux.QueryBuilderFormWindow.superclass.constructor.call(this, config);
	this.addEvents('save');
}

Ext.extend(Ext.ux.QueryBuilderFormWindow, Ext.Window, {
	modal:true,
	width:250, 
	height:120,		
	title:'Save Query',
	resizable:false,
	closeAction:'hide', 
	layout:'fit',
	formDetails:{},
	show:function( opener, resetOnShow ){
		if(resetOnShow){
			this.formPanel.form.reset();
		}
		Ext.ux.QueryBuilderFormWindow.superclass.show.call( this, opener );
	},	
	saveFormDetails : function(hide){	
		this.formPanel.form.submit({			
			waitMsg:'Please Wait....',
			success:function(form, action){	
				if(action.result.id){
					this.idField.setValue( action.result.id );				
				}
				this.fireEvent('save', this, this.idField.getValue() );
				if (hide){
					this.hide();
				}
			},
			failure: function(form, action) {
				if(action.failureType == 'client'){					
					Ext.MessageBox.alert('Error', 'Error in form');			
				} else {
					Ext.MessageBox.alert('Error', action.result.feedback);
				}
			},
			scope: this
		});	
	}
});