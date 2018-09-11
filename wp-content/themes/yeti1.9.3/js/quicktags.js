(function(){

	// private stuff is prefixed with an underscore
	var _domReady = function(func) {
		var t, i,  DOMContentLoaded;

		if ( typeof jQuery != 'undefined' ) {
			jQuery(document).ready(func);
		} else {
			t = _domReady;
			t.funcs = [];

			t.ready = function() {
				if ( ! t.isReady ) {
					t.isReady = true;
					for ( i = 0; i < t.funcs.length; i++ ) {
						t.funcs[i]();
					}
				}
			};

			if ( t.isReady ) {
				func();
			} else {
				t.funcs.push(func);
			}

			if ( ! t.eventAttached ) {
				if ( document.addEventListener ) {
					DOMContentLoaded = function(){document.removeEventListener('DOMContentLoaded', DOMContentLoaded, false);t.ready();};
					document.addEventListener('DOMContentLoaded', DOMContentLoaded, false);
					window.addEventListener('load', t.ready, false);
				} else if ( document.attachEvent ) {
					DOMContentLoaded = function(){if (document.readyState === 'complete'){ document.detachEvent('onreadystatechange', DOMContentLoaded);t.ready();}};
					document.attachEvent('onreadystatechange', DOMContentLoaded);
					window.attachEvent('onload', t.ready);

					(function(){
						try {
							document.documentElement.doScroll("left");
						} catch(e) {
							setTimeout(arguments.callee, 50);
							return;
						}

						t.ready();
					})();
				}

				t.eventAttached = true;
			}
		}
	},
	qt;

	qt = QTags = function(settings) {
		if ( typeof(settings) == 'string' )
			settings = {id: settings};
		else if ( typeof(settings) != 'object' )
			return false;

		var t = this,
			id = settings.id,
			canvas = document.getElementById(id),
			name = 'qt_' + id,
			tb, onclick, toolbar_id;

		if ( !id || !canvas )
			return false;

		t.name = name;
		t.id = id;
		t.canvas = canvas;
		t.settings = settings;

		if ( id == 'content' && typeof(adminpage) == 'string' && ( adminpage == 'post-new-php' || adminpage == 'post-php' ) ) {
			// back compat hack :-(
			edCanvas = canvas;
			toolbar_id = 'ed_toolbar';
		} else {
			toolbar_id = name + '_toolbar';
		}

		tb = document.createElement('div');
		tb.id = toolbar_id;
		tb.className = 'quicktags-toolbar';

		canvas.parentNode.insertBefore(tb, canvas);
		t.toolbar = tb;

		// listen for click events
		onclick = function(e) {
			e = e || window.event;
			var target = e.target || e.srcElement, visible = target.clientWidth || target.offsetWidth, i;

			// don't call the callback on pressing the accesskey when the button is not visible
			if ( !visible )
				return;

			// as long as it has the class ed_button, execute the callback
			if ( / ed_button /.test(' ' + target.className + ' ') ) {
				// we have to reassign canvas here
				t.canvas = canvas = document.getElementById(id);
				i = target.id.replace(name + '_', '');

				if ( t.theButtons[i] )
					t.theButtons[i].callback.call(t.theButtons[i], target, canvas, t);
			}
		};

		if ( tb.addEventListener ) {
			tb.addEventListener('click', onclick, false);
		} else if ( tb.attachEvent ) {
			tb.attachEvent('onclick', onclick);
		}

		t.getButton = function(id) {
			return t.theButtons[id];
		};

		t.getButtonElement = function(id) {
			return document.getElementById(name + '_' + id);
		};

		qt.instances[id] = t;

		if ( !qt.instances[0] ) {
			qt.instances[0] = qt.instances[id];
			_domReady( function(){ qt._buttonsInit(); } );
		}
	};

	qt.instances = {};

	qt.getInstance = function(id) {
		return qt.instances[id];
	};

	qt._buttonsInit = function() {
		var t = this, canvas, name, settings, theButtons, html, inst, ed, id, i, use,
			defaults = ',strong,em,link,block,del,ins,img,ul,ol,li,code,more,spell,close,';

		for ( inst in t.instances ) {
			if ( inst == 0 )
				continue;

			ed = t.instances[inst];
			canvas = ed.canvas;
			name = ed.name;
			settings = ed.settings;
			html = '';
			theButtons = {};
			use = '';

			// set buttons
			if ( settings.buttons )
				use = ','+settings.buttons+',';

			for ( i in edButtons ) {
				if ( !edButtons[i] )
					continue;

				id = edButtons[i].id;
				if ( use && defaults.indexOf(','+id+',') != -1 && use.indexOf(','+id+',') == -1 )
					continue;

				if ( !edButtons[i].instance || edButtons[i].instance == inst ) {
					theButtons[id] = edButtons[i];

					if ( edButtons[i].html )
						html += edButtons[i].html(name + '_');
				}
			}

			if ( use && use.indexOf(',fullscreen,') != -1 ) {
				theButtons['fullscreen'] = new qt.FullscreenButton();
				html += theButtons['fullscreen'].html(name + '_');
			}


			if ( 'rtl' == document.getElementsByTagName('html')[0].dir ) {
				theButtons['textdirection'] = new qt.TextDirectionButton();
				html += theButtons['textdirection'].html(name + '_');
			}

			ed.toolbar.innerHTML = html;
			ed.theButtons = theButtons;
		}
		t.buttonsInitDone = true;
	};

	qt.addButton = function( id, display, arg1, arg2, access_key, title, priority, instance ) {
		var btn;

		if ( !id || !display )
			return;

		priority = priority || 0;
		arg2 = arg2 || '';

		if ( typeof(arg1) === 'function' ) {
			btn = new qt.Button(id, display, access_key, title, instance);
			btn.callback = arg1;
		} else if ( typeof(arg1) === 'string' ) {
			btn = new qt.TagButton(id, display, arg1, arg2, access_key, title, instance);
		} else {
			return;
		}

		if ( priority == -1 ) // back-compat
			return btn;

		if ( priority > 0 ) {
			while ( typeof(edButtons[priority]) != 'undefined' ) {
				priority++
			}

			edButtons[priority] = btn;
		} else {
			edButtons[edButtons.length] = btn;
		}

		if ( this.buttonsInitDone )
			this._buttonsInit(); // add the button HTML to all instances toolbars if addButton() was called too late
	};

	qt.insertContent = function(content) {
		var sel, startPos, endPos, scrollTop, text, canvas = document.getElementById(wpActiveEditor);

		if ( !canvas )
			return false;

		if ( document.selection ) { //IE
			canvas.focus();
			sel = document.selection.createRange();
			sel.text = content;
			canvas.focus();
		} else if ( canvas.selectionStart || canvas.selectionStart == '0' ) { // FF, WebKit, Opera
			text = canvas.value;
			startPos = canvas.selectionStart;
			endPos = canvas.selectionEnd;
			scrollTop = canvas.scrollTop;

			canvas.value = text.substring(0, startPos) + content + text.substring(endPos, text.length);

			canvas.focus();
			canvas.selectionStart = startPos + content.length;
			canvas.selectionEnd = startPos + content.length;
			canvas.scrollTop = scrollTop;
		} else {
			canvas.value += content;
			canvas.focus();
		}
		return true;
	};

	// a plain, dumb button
	qt.Button = function(id, display, access, title, instance) {
		var t = this;
		t.id = id;
		t.display = display;
		t.access = access;
		t.title = title || '';
		t.instance = instance || '';
	};
	qt.Button.prototype.html = function(idPrefix) {
		var access = this.access ? ' accesskey="' + this.access + '"' : '';
		return '<input type="button" id="' + idPrefix + this.id + '"' + access + ' class="ed_button" value="' + this.display + '" onclick="ToolBtn(this,\''+this.id+'\')" />';
	};
	qt.Button.prototype.callback = function(){};

	// a button that inserts HTML tag
	qt.TagButton = function(id, display, tagStart, tagEnd, access, title, instance) {
		var t = this;
		qt.Button.call(t, id, display, access, title, instance);
		t.tagStart = tagStart;
		t.tagEnd = tagEnd;
	};
	qt.TagButton.prototype = new qt.Button();
	qt.TagButton.prototype.openTag = function(e, ed) {
		var t = this;

		if ( ! ed.openTags ) {
			ed.openTags = [];
		}
		if ( t.tagEnd ) {
			ed.openTags.push(t.id);
			e.value = '/' + e.value;
		}
	};
	qt.TagButton.prototype.closeTag = function(e, ed) {
		var t = this, i = t.isOpen(ed);

		if ( i !== false ) {
			ed.openTags.splice(i, 1);
		}

		e.value = t.display;
	};
	// whether a tag is open or not. Returns false if not open, or current open depth of the tag
	qt.TagButton.prototype.isOpen = function (ed) {
		var t = this, i = 0, ret = false;
		if ( ed.openTags ) {
			while ( ret === false && i < ed.openTags.length ) {
				ret = ed.openTags[i] == t.id ? i : false;
				i ++;
			}
		} else {
			ret = false;
		}
		return ret;
	};
	qt.TagButton.prototype.callback = function(element, canvas, ed) {
		var t = this, startPos, endPos, cursorPos, scrollTop, v = canvas.value, l, r, i, sel, endTag = v ? t.tagEnd : '';

		if ( document.selection ) { // IE
			canvas.focus();
			sel = document.selection.createRange();
			if ( sel.text.length > 0 ) {
				if ( !t.tagEnd )
					sel.text = sel.text + t.tagStart;
				else
					sel.text = t.tagStart + sel.text + endTag;
			} else {
				if ( !t.tagEnd ) {
					sel.text = t.tagStart;
				} else if ( t.isOpen(ed) === false ) {
					sel.text = t.tagStart;
					t.openTag(element, ed);
				} else {
					sel.text = endTag;
					t.closeTag(element, ed);
				}
			}
			canvas.focus();
		} else if ( canvas.selectionStart || canvas.selectionStart == '0' ) { // FF, WebKit, Opera
			startPos = canvas.selectionStart;
			endPos = canvas.selectionEnd;
			cursorPos = endPos;
			scrollTop = canvas.scrollTop;
			l = v.substring(0, startPos); // left of the selection
			r = v.substring(endPos, v.length); // right of the selection
			i = v.substring(startPos, endPos); // inside the selection
			if ( startPos != endPos ) {
				if ( !t.tagEnd ) {
					canvas.value = l + i + t.tagStart + r; // insert self closing tags after the selection
					cursorPos += t.tagStart.length;
				} else {
					canvas.value = l + t.tagStart + i + endTag + r;
					cursorPos += t.tagStart.length + endTag.length;
				}
			} else {
				if ( !t.tagEnd ) {
					canvas.value = l + t.tagStart + r;
					cursorPos = startPos + t.tagStart.length;
				} else if ( t.isOpen(ed) === false ) {
					canvas.value = l + t.tagStart + r;
					t.openTag(element, ed);
					cursorPos = startPos + t.tagStart.length;
				} else {
					canvas.value = l + endTag + r;
					cursorPos = startPos + endTag.length;
					t.closeTag(element, ed);
				}
			}

			canvas.focus();
			canvas.selectionStart = cursorPos;
			canvas.selectionEnd = cursorPos;
			canvas.scrollTop = scrollTop;
		} else { // other browsers?
			if ( !endTag ) {
				canvas.value += t.tagStart;
			} else if ( t.isOpen(ed) !== false ) {
				canvas.value += t.tagStart;
				t.openTag(element, ed);
			} else {
				canvas.value += endTag;
				t.closeTag(element, ed);
			}
			canvas.focus();
		}
	};




	edButtons[edButtons.length] = new edButton('image','图片','','','');
	edButtons[edButtons.length] = new edButton('lnk','链接','','','');
	edButtons[edButtons.length] = new edButton('weixin','微信','','','');
	edButtons[edButtons.length] = new edButton('php','PHP','','','');


})();

function edInsertContent(which, myValue) {
	myField = document.getElementById(which);
	//IE support
	if (document.selection) {
		myField.focus();
		sel = document.selection.createRange();
		sel.text = myValue;
		myField.focus();
	}
	//MOZILLA/NETSCAPE support
	else if (myField.selectionStart || myField.selectionStart == '0') {
		var startPos = myField.selectionStart;
		var endPos = myField.selectionEnd;
		var scrollTop = myField.scrollTop;
		myField.value = myField.value.substring(0, startPos)
						+ myValue 
						+ myField.value.substring(endPos, myField.value.length);
		myField.focus();
		myField.selectionStart = startPos + myValue.length;
		myField.selectionEnd = startPos + myValue.length;
		myField.scrollTop = scrollTop;
	} else {
		myField.value += myValue;
		myField.focus();
	}
}

function edTagOpened(btn) {
	var name = jQuery(btn).val();
	if (/\//.test(name)) return true;
	else return false;
}
function edAddTag(btn) {
	var name = jQuery(btn).val();
	if (/\//.test(name)) return;
	else jQuery(btn).val('/'+name);
}
function edRemoveTag(btn) {
	var name = jQuery(btn).val();
	if (/\//.test(name)) jQuery(btn).val(name.replace('/',''));
}
function edInsertTag(which, tagStart, tagEnd, btn) {
    myField = document.getElementById(which);
	//IE support
	if (document.selection) {
		myField.focus();
	    sel = document.selection.createRange();
		if (sel.text.length > 0) {
			sel.text = tagStart + sel.text + tagEnd;
		} else {
			if (!edTagOpened(btn)) {
				sel.text = tagStart;
				edAddTag(btn);
			} else {
				sel.text = tagEnd;
				edRemoveTag(btn);
			}
		}
		myField.focus();
	}
	//MOZILLA/NETSCAPE support
	else if (myField.selectionStart || myField.selectionStart == '0') {
		var startPos = myField.selectionStart;
		var endPos = myField.selectionEnd;
		var cursorPos = endPos;
		var scrollTop = myField.scrollTop;
		if (startPos != endPos) {
			myField.value = myField.value.substring(0, startPos)
			              + tagStart
			              + myField.value.substring(startPos, endPos) 
			              + tagEnd
			              + myField.value.substring(endPos, myField.value.length);
			cursorPos += tagStart.length + tagEnd.length;
		} else {
			if (!edTagOpened(btn)) {
				myField.value = myField.value.substring(0, startPos) 
				              + tagStart
				              + myField.value.substring(endPos, myField.value.length);
				edAddTag(btn);
				cursorPos = startPos + tagStart.length;
			} else {
				myField.value = myField.value.substring(0, startPos) 
				              + tagEnd
				              + myField.value.substring(endPos, myField.value.length);
				edRemoveTag(btn);
				cursorPos = startPos + tagEnd.length;
			}
		}
		myField.focus();
		myField.selectionStart = cursorPos;
		myField.selectionEnd = cursorPos;
		myField.scrollTop = scrollTop;
	} else {
		if (!edTagOpened(btn)) {
			myField.value += tagStart;
			edAddTag(btn);
		} else {
			myField.value += tagEnd;
			edRemoveTag(btn);
		}
		myField.focus();
	}
}


function ToolBtn(_this, name) {
	var which = jQuery(_this).parent().parent().find("textarea").attr('id');
	var myValue = '';
	switch (name) {
		case 'image' :
			myValue = HTML_img();
			break;
		case 'lnk' :
			HTML_link(_this, which);
			break;
		case 'weixin' :
			myValue = HTML_weixin();
			break;
		case 'php' :
			HTML_php(_this, which);
			break;
	}
	if (myValue) edInsertContent(which, myValue);
}
function HTML_img() {
	var src = prompt(quicktagsL10n.enterImageURL, 'http://');
	if (src) {
		var alt = prompt(quicktagsL10n.enterImageDescription, '');
		return '<img src="' + src + '" alt="' + alt + '" />';
	}
}
function HTML_link(btn, which) {
	if ( !edTagOpened(btn) ) {
		var URL = prompt(quicktagsL10n.enterURL,'http://');
		if (URL) {
			var tagS = '<a href="'+URL+'" target="_blank">';
			edInsertTag(which, tagS, '</a>', btn);
		}
	} else {
		edInsertTag(which,'','</a>', btn);
	}
}
function HTML_weixin() {
	var src = prompt('粘贴微信二维码图片URL到这里:', 'http://');
	if (src) return '<img src="' + src + '" alt="微信" class="weixin" />';
}
function HTML_php(btn, which) {
	edInsertTag(which,'[php]','[/php]', btn);
}
