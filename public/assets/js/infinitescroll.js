/*global jQuery: true */
/*!
--------------------------------
Infinite Scroll
--------------------------------
+ https://github.com/paulirish/infinite-scroll
+ version 2.1.0
+ Copyright 2011/12 Paul Irish & Luke Shumard
+ Licensed under the MIT license
+ Documentation: http://infinite-scroll.com/
*/
// Uses AMD or browser globals to create a jQuery plugin.
(function (factory) {
if (typeof define === 'function' && define.amd) {
// AMD. Register as an anonymous module.
define(['jquery'], factory);
} else {
// Browser globals
factory(jQuery);
}
}(function ($, undefined) {
'use strict';
$.infinitescroll = function infscr(options, callback, element) {
this.element = $(element);
// Flag the object in the event of a failed creation
if (!this._create(options, callback)) {
this.failed = true;
}
};
$.infinitescroll.defaults = {
loading: {
finished: undefined,
finishedMsg: "<em>Congratulations, you've reached the end of the internet.</em>",
img: 'data:image/gif;base64,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',
msg: null,
msgText: '<em>Loading the next set of posts...</em>',
selector: null,
speed: 'fast',
start: undefined
},
state: {
isDuringAjax: false,
isInvalidPage: false,
isDestroyed: false,
isDone: false, // For when it goes all the way through the archive.
isPaused: false,
isBeyondMaxPage: false,
currPage: 1
},
debug: false,
behavior: undefined,
binder: $(window), // used to cache the selector
nextSelector: 'div.navigation a:first',
navSelector: 'div.navigation',
contentSelector: null, // rename to pageFragment
extraScrollPx: 150,
itemSelector: 'div.post',
animate: false,
pathParse: undefined,
dataType: 'html',
appendCallback: true,
bufferPx: 40,
errorCallback: function () { },
infid: 0, //Instance ID
pixelsFromNavToBottom: undefined,
path: undefined, // Either parts of a URL as an array (e.g. ["/page/", "/"] or a function that takes in the page number and returns a URL
prefill: false, // When the document is smaller than the window, load data until the document is larger or links are exhausted
maxPage: undefined // to manually control maximum page (when maxPage is undefined, maximum page limitation is not work)
};
$.infinitescroll.prototype = {
/*
----------------------------
Private methods
----------------------------
*/
// Bind or unbind from scroll
_binding: function infscr_binding(binding) {
var instance = this,
opts = instance.options;
opts.v = '2.0b2.120520';
// if behavior is defined and this function is extended, call that instead of default
if (!!opts.behavior && this['_binding_'+opts.behavior] !== undefined) {
this['_binding_'+opts.behavior].call(this);
return;
}
if (binding !== 'bind' && binding !== 'unbind') {
this._debug('Binding value ' + binding + ' not valid');
return false;
}
if (binding === 'unbind') {
(this.options.binder).unbind('smartscroll.infscr.' + instance.options.infid);
} else {
(this.options.binder)[binding]('smartscroll.infscr.' + instance.options.infid, function () {
instance.scroll();
});
}
this._debug('Binding', binding);
},
// Fundamental aspects of the plugin are initialized
_create: function infscr_create(options, callback) {
// Add custom options to defaults
var opts = $.extend(true, {}, $.infinitescroll.defaults, options);
this.options = opts;
var $window = $(window);
var instance = this;
// Validate selectors
if (!instance._validate(options)) {
return false;
}
// Validate page fragment path
var path = $(opts.nextSelector).attr('href');
if (!path) {
this._debug('Navigation selector not found');
return false;
}
// Set the path to be a relative URL from root.
opts.path = opts.path || this._determinepath(path);
// contentSelector is 'page fragment' option for .load() / .ajax() calls
opts.contentSelector = opts.contentSelector || this.element;
// loading.selector - if we want to place the load message in a specific selector, defaulted to the contentSelector
opts.loading.selector = opts.loading.selector || opts.contentSelector;
// Define loading.msg
opts.loading.msg = opts.loading.msg || $('<div id="infscr-loading"><img alt="Loading..." src="' + opts.loading.img + '" /><div>' + opts.loading.msgText + '</div></div>');
// Preload loading.img
(new Image()).src = opts.loading.img;
// distance from nav links to bottom
// computed as: height of the document + top offset of container - top offset of nav link
if(opts.pixelsFromNavToBottom === undefined) {
opts.pixelsFromNavToBottom = $(document).height() - $(opts.navSelector).offset().top;
this._debug('pixelsFromNavToBottom: ' + opts.pixelsFromNavToBottom);
}
var self = this;
// determine loading.start actions
opts.loading.start = opts.loading.start || function() {
$(opts.navSelector).hide();
opts.loading.msg
.appendTo(opts.loading.selector)
.show(opts.loading.speed, $.proxy(function() {
this.beginAjax(opts);
}, self));
};
// determine loading.finished actions
opts.loading.finished = opts.loading.finished || function() {
if (!opts.state.isBeyondMaxPage)
opts.loading.msg.fadeOut(opts.loading.speed);
};
// callback loading
opts.callback = function(instance, data, url) {
if (!!opts.behavior && instance['_callback_'+opts.behavior] !== undefined) {
instance['_callback_'+opts.behavior].call($(opts.contentSelector)[0], data, url);
}
if (callback) {
callback.call($(opts.contentSelector)[0], data, opts, url);
}
if (opts.prefill) {
$window.bind('resize.infinite-scroll', instance._prefill);
}
};
if (options.debug) {
// Tell IE9 to use its built-in console
if (Function.prototype.bind && (typeof console === 'object' || typeof console === 'function') && typeof console.log === 'object') {
['log','info','warn','error','assert','dir','clear','profile','profileEnd']
.forEach(function (method) {
console[method] = this.call(console[method], console);
}, Function.prototype.bind);
}
}
this._setup();
// Setups the prefill method for use
if (opts.prefill) {
this._prefill();
}
// Return true to indicate successful creation
return true;
},
_prefill: function infscr_prefill() {
var instance = this;
var $window = $(window);
function needsPrefill() {
return ( $(instance.options.contentSelector).height() <= $window.height() );
}
this._prefill = function() {
if (needsPrefill()) {
instance.scroll();
}
$window.bind('resize.infinite-scroll', function() {
if (needsPrefill()) {
$window.unbind('resize.infinite-scroll');
instance.scroll();
}
});
};
// Call self after setting up the new function
this._prefill();
},
// Console log wrapper
_debug: function infscr_debug() {
if (true !== this.options.debug) {
return;
}
if (typeof console !== 'undefined' && typeof console.log === 'function') {
// Modern browsers
// Single argument, which is a string
if ((Array.prototype.slice.call(arguments)).length === 1 && typeof Array.prototype.slice.call(arguments)[0] === 'string') {
console.log( (Array.prototype.slice.call(arguments)).toString() );
} else {
console.log( Array.prototype.slice.call(arguments) );
}
} else if (!Function.prototype.bind && typeof console !== 'undefined' && typeof console.log === 'object') {
// IE8
Function.prototype.call.call(console.log, console, Array.prototype.slice.call(arguments));
}
},
// find the number to increment in the path.
_determinepath: function infscr_determinepath(path) {
var opts = this.options;
// if behavior is defined and this function is extended, call that instead of default
if (!!opts.behavior && this['_determinepath_'+opts.behavior] !== undefined) {
return this['_determinepath_'+opts.behavior].call(this,path);
}
if (!!opts.pathParse) {
this._debug('pathParse manual');
return opts.pathParse(path, this.options.state.currPage+1);
} else if (path.match(/^(.*?)\b2\b(.*?$)/)) {
path = path.match(/^(.*?)\b2\b(.*?$)/).slice(1);
// if there is any 2 in the url at all.
} else if (path.match(/^(.*?)2(.*?$)/)) {
// page= is used in django:
// http://www.infinite-scroll.com/changelog/comment-page-1/#comment-127
if (path.match(/^(.*?page=)2(\/.*|$)/)) {
path = path.match(/^(.*?page=)2(\/.*|$)/).slice(1);
return path;
}
path = path.match(/^(.*?)2(.*?$)/).slice(1);
} else {
// page= is used in drupal too but second page is page=1 not page=2:
// thx Jerod Fritz, vladikoff
if (path.match(/^(.*?page=)1(\/.*|$)/)) {
path = path.match(/^(.*?page=)1(\/.*|$)/).slice(1);
return path;
} else {
this._debug("Sorry, we couldn't parse your Next (Previous Posts) URL. Verify your the css selector points to the correct A tag. If you still get this error: yell, scream, and kindly ask for help at infinite-scroll.com.");
// Get rid of isInvalidPage to allow permalink to state
opts.state.isInvalidPage = true; //prevent it from running on this page.
}
}
this._debug('determinePath', path);
return path;
},
// Custom error
_error: function infscr_error(xhr) {
var opts = this.options;
// if behavior is defined and this function is extended, call that instead of default
if (!!opts.behavior && this['_error_'+opts.behavior] !== undefined) {
this['_error_'+opts.behavior].call(this,xhr);
return;
}
if (xhr !== 'destroy' && xhr !== 'end') {
xhr = 'unknown';
}
this._debug('Error', xhr);
if (xhr === 'end' || opts.state.isBeyondMaxPage) {
this._showdonemsg();
}
opts.state.isDone = true;
opts.state.currPage = 1; // if you need to go back to this instance
opts.state.isPaused = false;
opts.state.isBeyondMaxPage = false;
this._binding('unbind');
},
// Load Callback
_loadcallback: function infscr_loadcallback(box, data, url) {
var opts = this.options,
callback = this.options.callback, // GLOBAL OBJECT FOR CALLBACK
result = (opts.state.isDone) ? 'done' : (!opts.appendCallback) ? 'no-append' : 'append',
frag;
// if behavior is defined and this function is extended, call that instead of default
if (!!opts.behavior && this['_loadcallback_'+opts.behavior] !== undefined) {
this['_loadcallback_'+opts.behavior].call(this,box,data,url);
return;
}
switch (result) {
case 'done':
this._showdonemsg();
return false;
case 'no-append':
if (opts.dataType === 'html') {
data = '<div>' + data + '</div>';
data = $(data).find(opts.itemSelector);
}
// if it didn't return anything
if (data.length === 0) {
return this._error('end');
}
break;
case 'append':
var children = box.children();
// if it didn't return anything
if (children.length === 0) {
return this._error('end');
}
// use a documentFragment because it works when content is going into a table or UL
frag = document.createDocumentFragment();
while (box[0].firstChild) {
frag.appendChild(box[0].firstChild);
}
this._debug('contentSelector', $(opts.contentSelector)[0]);
$(opts.contentSelector)[0].appendChild(frag);
// previously, we would pass in the new DOM element as context for the callback
// however we're now using a documentfragment, which doesn't have parents or children,
// so the context is the contentContainer guy, and we pass in an array
// of the elements collected as the first argument.
data = children.get();
break;
}
// loadingEnd function
opts.loading.finished.call($(opts.contentSelector)[0],opts);
// smooth scroll to ease in the new content
if (opts.animate) {
var scrollTo = $(window).scrollTop() + $(opts.loading.msg).height() + opts.extraScrollPx + 'px';
$('html,body').animate({ scrollTop: scrollTo }, 800, function () { opts.state.isDuringAjax = false; });
}
if (!opts.animate) {
// once the call is done, we can allow it again.
opts.state.isDuringAjax = false;
}
callback(this, data, url);
if (opts.prefill) {
this._prefill();
}
},
_nearbottom: function infscr_nearbottom() {
var opts = this.options,
pixelsFromWindowBottomToBottom = 0 + $(document).height() - (opts.binder.scrollTop()) - $(window).height();
// if behavior is defined and this function is extended, call that instead of default
if (!!opts.behavior && this['_nearbottom_'+opts.behavior] !== undefined) {
return this['_nearbottom_'+opts.behavior].call(this);
}
this._debug('math:', pixelsFromWindowBottomToBottom, opts.pixelsFromNavToBottom);
// if distance remaining in the scroll (including buffer) is less than the orignal nav to bottom....
return (pixelsFromWindowBottomToBottom - opts.bufferPx < opts.pixelsFromNavToBottom);
},
// Pause / temporarily disable plugin from firing
_pausing: function infscr_pausing(pause) {
var opts = this.options;
// if behavior is defined and this function is extended, call that instead of default
if (!!opts.behavior && this['_pausing_'+opts.behavior] !== undefined) {
this['_pausing_'+opts.behavior].call(this,pause);
return;
}
// If pause is not 'pause' or 'resume', toggle it's value
if (pause !== 'pause' && pause !== 'resume' && pause !== null) {
this._debug('Invalid argument. Toggling pause value instead');
}
pause = (pause && (pause === 'pause' || pause === 'resume')) ? pause : 'toggle';
switch (pause) {
case 'pause':
opts.state.isPaused = true;
break;
case 'resume':
opts.state.isPaused = false;
break;
case 'toggle':
opts.state.isPaused = !opts.state.isPaused;
break;
}
this._debug('Paused', opts.state.isPaused);
return false;
},
// Behavior is determined
// If the behavior option is undefined, it will set to default and bind to scroll
_setup: function infscr_setup() {
var opts = this.options;
// if behavior is defined and this function is extended, call that instead of default
if (!!opts.behavior && this['_setup_'+opts.behavior] !== undefined) {
this['_setup_'+opts.behavior].call(this);
return;
}
this._binding('bind');
return false;
},
// Show done message
_showdonemsg: function infscr_showdonemsg() {
var opts = this.options;
// if behavior is defined and this function is extended, call that instead of default
if (!!opts.behavior && this['_showdonemsg_'+opts.behavior] !== undefined) {
this['_showdonemsg_'+opts.behavior].call(this);
return;
}
opts.loading.msg
.find('img')
.hide()
.parent()
.find('div').html(opts.loading.finishedMsg).animate({ opacity: 1 }, 2000, function () {
$(this).parent().fadeOut(opts.loading.speed);
});
// user provided callback when done
opts.errorCallback.call($(opts.contentSelector)[0],'done');
},
// grab each selector option and see if any fail
_validate: function infscr_validate(opts) {
for (var key in opts) {
if (key.indexOf && key.indexOf('Selector') > -1 && $(opts[key]).length === 0) {
this._debug('Your ' + key + ' found no elements.');
return false;
}
}
return true;
},
/*
----------------------------
Public methods
----------------------------
*/
// Bind to scroll
bind: function infscr_bind() {
this._binding('bind');
},
// Destroy current instance of plugin
destroy: function infscr_destroy() {
this.options.state.isDestroyed = true;
this.options.loading.finished();
return this._error('destroy');
},
// Set pause value to false
pause: function infscr_pause() {
this._pausing('pause');
},
// Set pause value to false
resume: function infscr_resume() {
this._pausing('resume');
},
beginAjax: function infscr_ajax(opts) {
var instance = this,
path = opts.path,
box, desturl, method, condition;
// increment the URL bit. e.g. /page/3/
opts.state.currPage++;
// Manually control maximum page
if ( opts.maxPage !== undefined && opts.state.currPage > opts.maxPage ){
opts.state.isBeyondMaxPage = true;
this.destroy();
return;
}
// if we're dealing with a table we can't use DIVs
box = $(opts.contentSelector).is('table, tbody') ? $('<tbody/>') : $('<div/>');
desturl = (typeof path === 'function') ? path(opts.state.currPage) : path.join(opts.state.currPage);
instance._debug('heading into ajax', desturl);
method = (opts.dataType === 'html' || opts.dataType === 'json' ) ? opts.dataType : 'html+callback';
if (opts.appendCallback && opts.dataType === 'html') {
method += '+callback';
}
switch (method) {
case 'html+callback':
instance._debug('Using HTML via .load() method');
box.load(desturl + ' ' + opts.itemSelector, undefined, function infscr_ajax_callback(responseText) {
instance._loadcallback(box, responseText, desturl);
});
break;
case 'html':
instance._debug('Using ' + (method.toUpperCase()) + ' via $.ajax() method');
$.ajax({
// params
url: desturl,
dataType: opts.dataType,
complete: function infscr_ajax_callback(jqXHR, textStatus) {
condition = (typeof (jqXHR.isResolved) !== 'undefined') ? (jqXHR.isResolved()) : (textStatus === 'success' || textStatus === 'notmodified');
if (condition) {
instance._loadcallback(box, jqXHR.responseText, desturl);
} else {
instance._error('end');
}
}
});
break;
case 'json':
instance._debug('Using ' + (method.toUpperCase()) + ' via $.ajax() method');
$.ajax({
dataType: 'json',
type: 'GET',
url: desturl,
success: function (data, textStatus, jqXHR) {
condition = (typeof (jqXHR.isResolved) !== 'undefined') ? (jqXHR.isResolved()) : (textStatus === 'success' || textStatus === 'notmodified');
if (opts.appendCallback) {
// if appendCallback is true, you must defined template in options.
// note that data passed into _loadcallback is already an html (after processed in opts.template(data)).
if (opts.template !== undefined) {
var theData = opts.template(data);
box.append(theData);
if (condition) {
instance._loadcallback(box, theData);
} else {
instance._error('end');
}
} else {
instance._debug('template must be defined.');
instance._error('end');
}
} else {
// if appendCallback is false, we will pass in the JSON object. you should handle it yourself in your callback.
if (condition) {
instance._loadcallback(box, data, desturl);
} else {
instance._error('end');
}
}
},
error: function() {
instance._debug('JSON ajax request failed.');
instance._error('end');
}
});
break;
}
},
// Retrieve next set of content items
retrieve: function infscr_retrieve(pageNum) {
pageNum = pageNum || null;
var instance = this,
opts = instance.options;
// if behavior is defined and this function is extended, call that instead of default
if (!!opts.behavior && this['retrieve_'+opts.behavior] !== undefined) {
this['retrieve_'+opts.behavior].call(this,pageNum);
return;
}
// for manual triggers, if destroyed, get out of here
if (opts.state.isDestroyed) {
this._debug('Instance is destroyed');
return false;
}
// we dont want to fire the ajax multiple times
opts.state.isDuringAjax = true;
opts.loading.start.call($(opts.contentSelector)[0],opts);
},
// Check to see next page is needed
scroll: function infscr_scroll() {
var opts = this.options,
state = opts.state;
// if behavior is defined and this function is extended, call that instead of default
if (!!opts.behavior && this['scroll_'+opts.behavior] !== undefined) {
this['scroll_'+opts.behavior].call(this);
return;
}
if (state.isDuringAjax || state.isInvalidPage || state.isDone || state.isDestroyed || state.isPaused) {
return;
}
if (!this._nearbottom()) {
return;
}
this.retrieve();
},
// Toggle pause value
toggle: function infscr_toggle() {
this._pausing();
},
// Unbind from scroll
unbind: function infscr_unbind() {
this._binding('unbind');
},
// update options
update: function infscr_options(key) {
if ($.isPlainObject(key)) {
this.options = $.extend(true,this.options,key);
}
}
};
/*
----------------------------
Infinite Scroll function
----------------------------
Borrowed logic from the following...
jQuery UI
- https://github.com/jquery/jquery-ui/blob/master/ui/jquery.ui.widget.js
jCarousel
- https://github.com/jsor/jcarousel/blob/master/lib/jquery.jcarousel.js
Masonry
- https://github.com/desandro/masonry/blob/master/jquery.masonry.js
*/
$.fn.infinitescroll = function infscr_init(options, callback) {
var thisCall = typeof options;
switch (thisCall) {
// method
case 'string':
var args = Array.prototype.slice.call(arguments, 1);
this.each(function () {
var instance = $.data(this, 'infinitescroll');
if (!instance) {
// not setup yet
// return $.error('Method ' + options + ' cannot be called until Infinite Scroll is setup');
return false;
}
if (!$.isFunction(instance[options]) || options.charAt(0) === '_') {
// return $.error('No such method ' + options + ' for Infinite Scroll');
return false;
}
// no errors!
instance[options].apply(instance, args);
});
break;
// creation
case 'object':
this.each(function () {
var instance = $.data(this, 'infinitescroll');
if (instance) {
// update options of current instance
instance.update(options);
} else {
// initialize new instance
instance = new $.infinitescroll(options, callback, this);
// don't attach if instantiation failed
if (!instance.failed) {
$.data(this, 'infinitescroll', instance);
}
}
});
break;
}
return this;
};
/*
* smartscroll: debounced scroll event for jQuery *
* https://github.com/lukeshumard/smartscroll
* Based on smartresize by @louis_remi: https://github.com/lrbabe/jquery.smartresize.js *
* Copyright 2011 Louis-Remi & Luke Shumard * Licensed under the MIT license. *
*/
var event = $.event,
scrollTimeout;
event.special.smartscroll = {
setup: function () {
$(this).bind('scroll', event.special.smartscroll.handler);
},
teardown: function () {
$(this).unbind('scroll', event.special.smartscroll.handler);
},
handler: function (event, execAsap) {
// Save the context
var context = this,
args = arguments;
// set correct event type
event.type = 'smartscroll';
if (scrollTimeout) { clearTimeout(scrollTimeout); }
scrollTimeout = setTimeout(function () {
$(context).trigger('smartscroll', args);
}, execAsap === 'execAsap' ? 0 : 100);
}
};
$.fn.smartscroll = function (fn) {
return fn ? this.bind('smartscroll', fn) : this.trigger('smartscroll', ['execAsap']);
};
}));