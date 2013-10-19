/**
 * jQuery custom event "leftClick, rightClick, wheelClick"
 * http://jsnt.blog.fc2.com/
 * 
 * Copyright (c) 2011 yuqq (yuqq.js@gmail.com)
 * Dual licensed under the MIT and GPL licenses.
 *
 * Date: 2012-01-13
 */
(function()
{

	(function($, LEFT_CLICK, RIGHT_CLICK, WHEEL_CLICK)
	{
		
		var target = null;
    //var modern = ( !$.browser.msie || $.browser.version >= 9 );
    var modern = true;

		function aim(e)
		{
			target = this;
			return false;
		}
		
		function reset(e)
		{
			target = null;
		}
		
		function checkLeft(e)
		{
			if ( target == this && ( (modern && e.button == 0) || (!modern && e.button == 1) ) )
			{
				$.event.trigger(LEFT_CLICK, e, this);
			}
		}
		
		function checkWheel(e)
		{
			if ( target == this && ( (modern && e.button == 1) || (!modern && e.button == 4) ) )
			{
				$.event.trigger(WHEEL_CLICK, e, this);
			}		
		}
		
		$.event.special[LEFT_CLICK] = {
			
			setup: function()
			{
				$.event.add(this, 'mouseup', checkLeft);
				if ( !this._state )
				{
					$.event.add(this, 'mousedown', aim);
					$.event.add(document, 'mouseup', reset);
				}
				this._state = this._state | 1;
			},
			
			teardown: function()
			{
				$.event.remove(this, 'mouseup', checkLeft);
				this._state = this._state ^ 1;
				if ( !this._state )
				{
					$.event.remove(this, 'mousedown', aim);
					$.event.remove(document, 'mouseup', reset);
				}
			}
			
		};

		$.event.special[RIGHT_CLICK] = {
				delegateType: "contextmenu",
				bindType: "contextmenu" 
		};
		
		$.event.special[WHEEL_CLICK] = {
			
			setup: function()
			{
				$.event.add(this, 'mouseup', checkWheel);
				if ( !this._state )
				{
					$.event.add(this, 'mousedown', aim);
					$.event.add(document, 'mouseup', reset);
				}
				this._state = this._state | 4;
			},
			
			teardown: function()
			{
				$.event.remove(this, 'mouseup', checkWheel);
				this._state = this._state ^ 4;
				if ( !this._state )
				{
					$.event.remove(this, 'mousedown', aim);
					$.event.remove(document, 'mouseup', reset);
				}
			}
			
		};
	
		$.fn[LEFT_CLICK] = function(fn)
		{
			return fn ? this.bind(LEFT_CLICK, fn) : this.trigger(LEFT_CLICK);
		};
	
		$.fn[RIGHT_CLICK] = function(fn)
		{
			return fn ? this.bind("contextmenu", fn) : this.trigger("contextmenu");
		};	
		
		$.fn[WHEEL_CLICK] = function(fn)
		{
			return fn ? this.bind(WHEEL_CLICK, fn) : this.trigger(WHEEL_CLICK);
		};
			
	})(jQuery, 'leftClick', 'rightClick', 'wheelClick');

})();
