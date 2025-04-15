$(function(){
    $('[data-bs-toggle="popover"]').popover();
    
    $('body').off('click', '.tabs-box .tabs-btns');
    $('body').on('click', '.tabs-btns', function(event){
        var _this = $(this);
        if(_this.hasClass('actives')){
        }else{
            _this.closest('.tab-content').find('.tab-pane').removeClass('active show');
            _this.closest('.tab-content').find('.tabs-btns').removeClass('actives');
            _this.addClass('actives');
            _this.next('.tab-pane').addClass('active show');
            var _index = _this.index() / 2;
            _this.closest('.tabs-box').find('.nav-tabs .nav-item .nav-link').removeClass('active')
            _this.closest('.tabs-box').find('.nav-tabs .nav-item').eq(_index).find('.nav-link').addClass('active');
        }
    });
    
    /*还缺自带切换来添加自己的active*/
    $('body').off('click', '.tabs-box .nav-tabs .nav-item');
    $('body').on('click', '.tabs-box .nav-tabs .nav-item', function(event){
        var _this = $(this);
        var _index = _this.index();
        _this.closest('.tabs-box').find('.tab-content .tabs-btns').removeClass('actives')
        _this.closest('.tabs-box').find('.tab-content .tabs-btns').eq(_index).addClass('actives')
    });
    
    $('body').off('click', '.navbar-nav .nav-item .drop .toggle');
    $('body').on('click', '.navbar-nav .nav-item .drop .toggle', function(event){
        var _this = $(this);
        
        _this.closest('.nav-item').siblings().not().find('.dropdown-menu').slideUp();
        _this.closest('.nav-item').siblings().removeClass('active');
        _this.closest('.nav-item').toggleClass('active').find('.dropdown-menu').slideToggle();
        
        event.stopPropagation();   // 阻止事件冒泡
        event.preventDefault();   // 阻止事件的默认行为
    });
    
    var contentWayPoint = function() {
        var i = 0;
        $('.animate-box').waypoint( function( direction ) {
            if( direction === 'down' && !$(this.element).hasClass('animated') ) {
                i++;
                $(this.element).addClass('item-animate');
                setTimeout(function(){
                    $('body .animate-box.item-animate').each(function(k){
                        var el = $(this);
                        setTimeout( function () {
                            var effect = el.data('animate-effect');
                            if ( effect === 'fadeIn') {
                                el.addClass('fadeIn animated');
                            } else if ( effect === 'fadeInLeft') {
                                el.addClass('fadeInLeft animated');
                            } else if ( effect === 'fadeInRight') {
                                el.addClass('fadeInRight animated');
                            } else {
                                el.addClass('fadeInUp animated');
                            }
                            el.removeClass('item-animate');
                        },  k * 200, 'easeInOutExpo' );
                    });
                }, 100);
            }
        } , { offset: '85%' } );
    };
    contentWayPoint();
})