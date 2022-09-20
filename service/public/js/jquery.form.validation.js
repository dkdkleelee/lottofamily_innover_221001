// form validation and uri maker plugin
//
// Date		: 2009-07-16
// Author	: invaderx
// Desc.	: <input type="text" name="name" class="input required al" title="�̸�">
//			: �Է°� ���� ��, �Է°� ����[���ĺ��� ���] üũ

(function($) {
	$.fn.validation = function(expression) {
	//alert(this.attr('name'));
	//	alert($('#contract').name);
		//var formFields = Form.getElements(this);

		return this.each(function() {
			//$this = $(this);
			$(this).submit(function(e) {
				
				//$('.required', this).each(function() {alert('1');});
				$.fn.validation.checkFormValues(e, $(this));
			});
		});
	};
		
	$.fn.validation.checkFormValues = function(e, obj) {

		var check = true;
		var checkExp = { num : {title : '����', exp : /^[0-9]*$/}, al : {title : '����', exp : /^[A-Za-z]*$/}, alnum : {title : '����, ����', exp : /^[A-Za-z0-9]*$/}, alnumu : {title : '����, ����, _', exp : /^[_A-Za-z0-9+]*$/}, jumin : {title : '�ֹι�ȣ', exp : /\d{6}(\-|)[1-4]\d{6}$/}};

		if(obj.hasClass('validation')) {
			$('.required, .num, .al, .alnum, jumin', obj).each(function() { 
				var tagName = this.tagName;
				var type = this.type;
				var name = this.name;
				var title = this.title;
				var value = this.value;
				var el = this;
	
				if($(this).hasClass('required')) {
					if(type.toLowerCase() == 'text' && 'file' || tagName.toLowerCase() == 'textarea') {
						if(!value) {
							alert(title+" �Է��� �ʼ� �Դϴ�.");
							if(tagName.toLowerCase() == 'textarea' && $(this).hasClass('editor')) {
								var oEditor = FCKeditorAPI.GetInstance(name) ;
								oEditor.Focus();
							} else {
								this.focus();
							}
							check = false;
							if (e && e.preventDefault) e.preventDefault();
							return false;
						}
					} else if(type.toLowerCase() == 'checkbox' || type.toLowerCase() == 'radio') {
						var checkboxArr = document.getElementsByName(name);
						var checked = false;
	
						$('input[name='+name+']', obj).each(function() {
							if(this.checked) checked = true;
						});
	
						if(!checked) {
							alert(title+" ������ �ʼ� �Դϴ�.");
							check = false;
							if (e && e.preventDefault) e.preventDefault();
							return false;
						}
					}
	
					if(tagName == 'select') {
						if(!value) {
							alert(title+" ������ �ʼ� �Դϴ�.");
							this.focus();
							check = false;
							if (e && e.preventDefault) e.preventDefault();
							return false;
						}
					}
				}
	
				// �Է����� üũ
				$.each(checkExp, function(className, setting) {
					if($(el).hasClass(className)) {
						var regCheck = new RegExp(setting.exp);
						if(!regCheck.test(value)) {
							alert(title+" �Է��� ["+setting.title+"] ���Ŀ� ���� �ʽ��ϴ�.");
							el.value = "";
							el.focus();
							check = false;
							if (e && e.preventDefault) e.preventDefault();
							return false;
						}
					}
				});
	
			});
		}

		// get Submit�� url���� ������ /aaa/111/bbb/222/ccc/333���� 
		if(obj.attr('method') == 'get' && check) {
			
			var params = obj.serialize();

			var params = obj.serialize().replace(/%2F/g,'\/').replace(/=/g,'\/').replace(/&/g,'\/');
			var action = obj.attr('action');
			
			//var submitUrl = /\/$/.test(action) ? action+params+'/' : action+'/'+params+'/';
			var submitUrl = /\/$/.test(action) ? action+params : action+'/'+params;
			if (e && e.preventDefault) e.preventDefault();
			location.href = submitUrl;
		}
	};

})(jQuery);

$(document).ready(function(){
	$('form').each(function() {
		$(this).validation();
	});
});