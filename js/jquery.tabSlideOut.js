/*
    tabSlideOUt v1.1
    
    By William Paoli: http://wpaoli.building58.com

    To use you must have an image ready to go as your tab
    Make sure to pass in at minimum the path to the image and its dimensions:
    
    example:
    
        $('.slide-out-div').tabSlideOut({
                tabHandle: '.handle',                         //class of the element that will be your tab -doesnt have to be an anchor
                pathToTabImage: 'images/contact_tab.gif',     //relative path to the image for the tab *required*
                imageHeight: '133px',                         //height of tab image *required*
                imageWidth: '44px',                           //width of tab image *required*    
        });

    
*/


(function($){
    $.fn.tabSlideOut = function(callerSettings) {
        var settings = $.extend({
            tabHandle: '.handle',
            speed: 300, 
            action: 'click',
            tabLocation: 'left',
            topPos: '200px',
            leftPos: '20px',
            fixedPosition: false, //false = positin:absolute, true = position:fixed
            pathToTabImage: null,
            imageHeight: null,
            imageWidth: null                       
        }, callerSettings||{});

        settings.tabHandle = $(settings.tabHandle);
        var obj = this;
        if (settings.fixedPosition === true) {
            var positioning = 'fixed';
        } else {
            var positioning = 'absolute';
        }
        
        //ie6 doesn't do well with the fixed option
        if (document.all && !window.opera && !window.XMLHttpRequest) {
            positioning = 'absolute';
        }
        
        
        
        //set initial tabHandle css
        settings.tabHandle.css({ 
            'display': 'block',
            'width' : settings.imageWidth,
            'height': settings.imageHeight,
            'textIndent' : '-99999px',
            'background' : 'url('+settings.pathToTabImage+') no-repeat',
            'outline' : 'none',
            'position' : 'absolute'
        });
        
        obj.css({'line-height' : '1'});

        
        var properties = {
                    containerWidth: obj.outerWidth(),
                    containerHeight: obj.outerHeight(),
                    containerPaddingTop: obj.css('paddingTop'),
                    containerPaddingBottom: obj.css('paddingBottom'),
                    containerPaddingLeft: parseInt(obj.css('paddingRight'), 10),
                    tabWidth: settings.tabHandle.outerWidth(),
                    tabHeight: settings.tabHandle.outerHeight()
                };

        //set calculated css

        
        if(settings.tabLocation === 'top') {

            var objTopCss = {
                'position' : 'absolute',
                'top' : '-' + parseInt(properties.containerHeight) + 'px',
                'left' : settings.leftPos
            };
            
            var handleTopCss = {
                'bottom' : '-' + ((parseInt(properties.tabHeight)) + 'px'),
                'right' : 0
            };

            obj.css(objTopCss);
            settings.tabHandle.css(handleTopCss);

        }

        
        
        if(settings.tabLocation === 'left') {
            var objLeftCss = {
                'height' : properties.containerHeight + 'px', //do this for when submit ajax form
                'left': '-' + properties.containerWidth + 'px',
                'top' : settings.topPos,
                'position' : positioning    
            };
        
            var handleLeftCss = {
                'top' :0,
                'right' : '-' + (parseInt(properties.tabWidth)) + 'px'
            };
        
            obj.css(objLeftCss);
            settings.tabHandle.css(handleLeftCss);
            
        }

        //functions for animation events
        
        settings.tabHandle.click(function(event){
            event.preventDefault();
        });
        
        var slideIn = function() {
            
            if (settings.tabLocation == 'top') {
                obj.animate({top:'-' + (parseInt(properties.containerHeight))+ 'px'}, settings.speed).removeClass('open');
            } else if (settings.tabLocation == 'left') {
                obj.animate({left: '-' + properties.containerWidth}, settings.speed).removeClass('open');
            }
            
        }
        
        var slideOut = function() {
            
            if (settings.tabLocation == 'top') {
                obj.animate({top:'-3px'},  settings.speed).addClass('open');
            } else if (settings.tabLocation == 'left') {
                obj.animate({left:'-3px'},  settings.speed).addClass('open');;
            }


        }


        var clickScreenToClose = function() {
            obj.click(function(event){
                event.stopPropagation();
            });
            
            $(document).click(function(){
                slideIn();
            });
        }
        
        var clickAction = function(){
            settings.tabHandle.click(function(event){
                if (obj.hasClass('open')) {
                    slideIn();
                } else {
                    slideOut();
                }
            });
            
            clickScreenToClose();
        }
        
        var hoverAction = function(){
            obj.hover(
                function(){
                    slideOut();
                },
                
                function(){
                    slideIn();
                });
                
                settings.tabHandle.click(function(event){
                    if (obj.hasClass('open')) {
                        slideIn();
                    }
                });
                clickScreenToClose();
                
        };
        
        //choose which type of action to bind
        if (settings.action === 'click') {
            clickAction();
        }
        
        if (settings.action === 'hover') {
            hoverAction();
        }
    };
})(jQuery);
