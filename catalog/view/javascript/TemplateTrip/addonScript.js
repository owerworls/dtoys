/*! Customized Jquery from Punit Korat.  punit@templatetrip.com  : www.templatetrip.com
Authors & copyright (c) 2016: TemplateTrip - Webzeel Services(addonScript). */
/*! NOTE: This Javascript is licensed under two options: a commercial license, a commercial OEM license and Copyright by Webzeel Services - For use Only with TemplateTrip Themes for our Customers*/
$(document).ready(function () {

    /* Slider Load Spinner */
    $(window).load(function () {
        $(".slideshow-panel .ttloading-bg").removeClass("ttloader");
    });
    /*------------- Slider -Loader Js Strat ---------------*/
    $(window).load(function () {
        $(".ttloading-bg").fadeOut("slow");
    });
    /*------------- Slider -Loader Js End ---------------*/

    $(".common-home .slideshow-panel, .common-home #ttcmsrightbanner, .common-home #ttcmsservices").wrapAll("<div class='main-slider'><div class='row'></div></div>");

    $(".option-filter .list-group-items a").click(function () {
        $(this).toggleClass('collapsed').next('.list-group-item').slideToggle();
    });

    $("ul.breadcrumb li:nth-last-child(1) a").addClass('last-breadcrumb').removeAttr('href');

    $("#column-left .products-list .product-thumb, #column-right .products-list .product-thumb").unwrap();

    $("#content > h1, .account-wishlist #content > h2, .account-address #content > h2, .account-download #content > h2").first().addClass("page-title");

    $("#column-left .product-thumb .button-group .btn-cart").removeAttr('data-original-title');

    $("#content > .page-title").wrap("<div class='page-title-wrapper'><div class='container'><div class='breadcrumb-wrapper'></div></div></div>");
    $(".page-title-wrapper .container .breadcrumb-wrapper").append($("ul.breadcrumb"));
    $("#content > .page-title-wrapper").appendTo($("body .header-content-title"));

    $('#column-left .product-thumb .image, #column-right .product-thumb .image').attr('class', 'image col-xs-5 col-sm-5 col-md-4');
    $('#column-left .product-thumb .thumb-description, #column-right .product-thumb .thumb-description').attr('class', 'thumb-description col-xs-7 col-sm-7 col-md-8');

    $("body").append("<div class='backtotop-img'><div class='goToTop ttbox'></div></div>");
    $("body").append("<div id='goToTop' title='Top' class='goToTop ttbox-img'></div>");
    $("#goToTop").hide();

    $('#content .row > .product-list .product-thumb .image').attr('class', 'image col-xs-5 col-sm-5 col-md-4');
    $('#content .row > .product-list .product-thumb .thumb-description').attr('class', 'thumb-description col-xs-7 col-sm-7 col-md-8');
    $('#content .row > .product-grid .product-thumb .image').attr('class', 'image col-xs-12');
    $('#content .row > .product-grid .product-thumb .thumb-description').attr('class', 'thumb-description col-xs-12');

    $('select.form-control').wrap("<div class='select-wrapper'></div>");
    $('input[type="checkbox"]:not(:checked)').wrap("<span class='checkbox-wrapper'></span>");
    $('input[type="checkbox"]:checked').wrap("<span class='checkbox-wrapper active'></span>");
    $('input[type="checkbox"]').attr('class', 'checkboxid');
    $('input[type="radio"]:not(:checked)').wrap("<span class='radio-wrapper'></span>");
    $('input[type="radio"]:checked').wrap("<span class='radio-wrapper active'></span>");
    $('input[type="radio"]').attr('class', 'radioid');

    $(".common-home #ttcmsbanner, .common-home #ttcmstestimonial").wrapAll("<div class='product-small-view'><div class='container'><div class='row'></div></div></div>");


    /*---------------------- Inputtype Js Start -----------------------------*/
    $('.checkboxid').change(function () {
        if ($(this).is(":checked")) {
            $(this).addClass("chkactive");
            $(this).parent().addClass('active');
        } else {
            $(this).removeClass("chkactive");
            $(this).parent().removeClass('active');
        }
    });

    $(function () {
        var $radioButtons = $('input[type="radio"]');
        $radioButtons.click(function () {
            $radioButtons.each(function () {
                $(this).parent().toggleClass('active', this.checked);
            });
        });
    });
    /*---------------------- Inputtype Js End -----------------------------*/

    /* Testimonial js */
    var tttestimonial = $("#tttestimonial-carousel");
    tttestimonial.owlCarousel({
        autoPlay: true,
        paginationNumbers: true,
        items: 1, //10 items above 1000px browser width
        itemsDesktop: [1200, 1],
        itemsDesktopSmall: [991, 1],
        itemsTablet: [767, 1],
        itemsMobile: [480, 1]
    });

    // Custom Navigation Events
    $(".tttestimonial_next").click(function () {
        tttestimonial.trigger('owl.next');
    })

    $(".tttestimonial_prev").click(function () {
        tttestimonial.trigger('owl.prev');
    });
    /* testimonial js over... */
    /* ----------- SmartBlog Js Start ----------- */
    var ttblog = $("#ttsmartblog-carousel");
    ttblog.owlCarousel({
        items: 3, //10 items above 1000px browser width
        itemsDesktop: [1200, 3],
        itemsDesktopSmall: [991, 2],
        itemsTablet: [767, 2],
        itemsMobile: [480, 1],
        navigation: true,
        pagination: false
    });

    // Custom Navigation Events

    $(".ttblog_next").click(function () {
        ttblog.trigger('owl.next');
    });
    $(".ttblog_prev").click(function () {
        ttblog.trigger('owl.prev');
    });
    /* ----------- SmartBlog Js End ----------- */
// Carousel Counter
    colsCarousel = $('#column-right, #column-left').length;
    if (colsCarousel == 2) {
        ci = 2;
    } else if (colsCarousel == 1) {
        ci = 3;
    } else {
        ci = 4;
    }

// product Carousel
    $("#content .products-carousel").owlCarousel({
        items: ci,
        itemsDesktop: [1200, 4],
        itemsDesktopSmall: [991, 3],
        itemsTablet: [767, 2],
        itemsMobile: [480, 1],
        navigation: true,
        pagination: false
    });

    $(".customNavigation .next").click(function () {
        $(this).parent().parent().find(".products-carousel").trigger('owl.next');
    });
    $(".customNavigation .prev").click(function () {
        $(this).parent().parent().find(".products-carousel").trigger('owl.prev');
    });
    $(".products-list .customNavigation").addClass('owl-navigation');


    /* Go to Top JS START */
    $(function () {
        $(window).scroll(function () {
            if ($(this).scrollTop() > 150) {
                $('.goToTop').fadeIn();
            } else {
                $('.goToTop').fadeOut();
            }
        });

        // scroll body to 0px on click
        $('.goToTop').click(function () {
            $('body,html').animate({
                scrollTop: 0
            }, 1000);
            return false;
        });
    });
    /* Go to Top JS END */

    $(".product-category .product-list .product-thumb .image .btn-merge").each(function () {
        $(this).appendTo($(this).parent().parent().find(".thumb-description .button-group"));
    });
    $(".product-category .product-grid .product-thumb .thumb-description .button-group .btn-merge").each(function () {
        $(this).appendTo($(this).parent().parent().parent().find(".image"));
    });


    /* Active class in Product List Grid START */
    $('#list-view').click(function () {
        $('#grid-view').removeClass('active');
        $('#list-view').addClass('active');
        $('#content .row > .product-list .product-thumb .image').attr('class', 'image col-xs-5 col-sm-5 col-md-4');
        $('#content .row > .product-list .product-thumb .thumb-description').attr('class', 'thumb-description col-xs-7 col-sm-7 col-md-8');
        $(".product-category .product-list .product-thumb .image .btn-merge").each(function () {
            $(this).appendTo($(this).parent().parent().find(".thumb-description .button-group"));
        });

    });
    $('#grid-view').click(function () {
        $('#list-view').removeClass('active');
        $('#grid-view').addClass('active');
        $('#content .row > .product-grid .product-thumb .image').attr('class', 'image col-xs-12');
        $('#content .row > .product-grid .product-thumb .thumb-description').attr('class', 'thumb-description col-xs-12');
        $(".product-category .product-grid .product-thumb .thumb-description .button-group .btn-merge").each(function () {
            $(this).appendTo($(this).parent().parent().parent().find(".image"));
        });
    });

    if (localStorage.getItem('display') == 'list') {
        $('#list-view').addClass('active');
    } else {
        $('#grid-view').addClass('active');
    }
    /* Active class in Product List Grid END */
});

// Documnet.ready() over....
function searchToggle() {
    if ($(window).width() >= 992) {
        $(".ttsearch_button").unbind("click");
        $(".ttsearch_button").click(function () {
            $('.ttsearchtoggle').parent().toggleClass('active');
            $('.ttsearchtoggle').toggle('fast', function () {
            });
            $('.ttsearchtoggle .input-lg').attr('autofocus', 'autofocus').focus();
            $(".account-link-toggle").slideUp("slow");
            $(".header-cart-toggle").slideUp("slow");
            $(".language-toggle").slideUp("slow");
            $(".currency-toggle").slideUp("fast");
        });

    }
    else {
        $(".ttsearch_button").unbind("click");
    }
}

$(document).ready(function () {
    searchToggle();
});
$(window).resize(function () {
    searchToggle();
});

function cartToggle() {
    if ($(window).width() >= 992) {
        $("#cart button.dropdown-toggle").unbind("click");
        $("#cart button.dropdown-toggle").click(function () {
            $(".header-cart-toggle").slideToggle("2000");
            $(".account-link-toggle").slideUp("slow");
            $(".language-toggle").slideUp("slow");
            $(".currency-toggle").slideUp("fast");
            $('.ttsearchtoggle').parent().removeClass("active");
            $('.ttsearchtoggle').hide('fast');
        });
    }
    else {
        $("#cart button.dropdown-toggle").unbind("click");
        $("#cart button.dropdown-toggle").click(function () {
            $(".header-cart-toggle").slideToggle("2000");
            $(".account-link-toggle").slideUp("slow");
            $(".language-toggle").slideUp("slow");
            $(".currency-toggle").slideUp("fast");
        });
    }
}

$(document).ready(function () {
    cartToggle();
});
$(window).resize(function () {
    cartToggle();
});


function currencyToggle() {
    if ($(window).width() >= 992) {
        $("#form-currency button.dropdown-toggle").unbind("click");
        $("#form-currency button.dropdown-toggle").click(function () {
            $(".currency-toggle").slideToggle("2000");
            $(".language-toggle").slideUp("slow");
            $(".account-link-toggle").slideUp("slow");
            $('.ttsearchtoggle').parent().removeClass("active");
            $('.ttsearchtoggle').hide('fast');
            $(".header-cart-toggle").slideUp("slow");

        });
    }
    else {
        $("#form-currency button.dropdown-toggle").unbind("click");
        $("#form-currency button.dropdown-toggle").click(function () {
            $(".currency-toggle").slideToggle("2000");
            $(".language-toggle").slideUp("slow");
            $(".account-link-toggle").slideUp("slow");
            $(".header-cart-toggle").slideUp("slow");
        });
    }
}

$(document).ready(function () {
    currencyToggle();
});
$(window).resize(function () {
    currencyToggle();
});

function languageToggle() {
    if ($(window).width() >= 992) {
        $("#form-language button.dropdown-toggle").unbind("click");
        $("#form-language button.dropdown-toggle").click(function () {
            $(".language-toggle").slideToggle("2000");
            $(".currency-toggle").slideUp("fast");
            $(".account-link-toggle").slideUp("slow");
            $('.ttsearchtoggle').parent().removeClass("active");
            $('.ttsearchtoggle').hide('fast');
            $(".header-cart-toggle").slideUp("slow");
        });
    }
    else {
        $("#form-language button.dropdown-toggle").unbind("click");
        $("#form-language button.dropdown-toggle").click(function () {
            $(".language-toggle").slideToggle("2000");
            $(".currency-toggle").slideUp("fast");
            $(".account-link-toggle").slideUp("slow");
            $(".header-cart-toggle").slideUp("slow");
        });
    }
}

$(document).ready(function () {
    languageToggle();
});
$(window).resize(function () {
    languageToggle();
});

function accountToggle() {
    if ($(window).width() >= 992) {
        $("#top-links a.dropdown-toggle").unbind("click");
        $("#top-links a.dropdown-toggle").click(function () {
            $(".account-link-toggle").slideToggle("2000");
            if ($("#form-currency")[0]) {
                $(".currency-toggle").css('display', 'none');
            }
            if ($("#form-language")[0]) {
                $(".language-toggle").css('display', 'none');
            }
            $(".header-cart-toggle").slideUp("slow");
            $('.ttsearchtoggle').parent().removeClass("active");
            $('.ttsearchtoggle').hide('fast');
        });
    }
    else {
        $("#top-links a.dropdown-toggle").unbind("click");
        $("#top-links a.dropdown-toggle").click(function () {
            $(".account-link-toggle").slideToggle("2000");
            if ($("#form-currency")[0]) {
                $(".currency-toggle").css('display', 'none');
            }
            if ($("#form-language")[0]) {
                $(".language-toggle").css('display', 'none');
            }
            $(".header-cart-toggle").slideUp("slow");
        });
    }
}

$(document).ready(function () {
    accountToggle();
});
$(window).resize(function () {
    accountToggle();
});


function footerToggle() {
    if ($(window).width() < 992) {
        $("footer .footer-column h5").addClass("toggle");
        $("footer .footer-column ul").css('display', 'none');
        $("footer .footer-column.active ul").css('display', 'block');
        $("footer .footer-column h5.toggle").unbind("click");
        $("footer .footer-column h5.toggle").click(function () {
            $(this).parent().toggleClass('active').find('ul.list-unstyled').slideToggle("slow");
        });
    } else {
        $("footer .footer-column h5").removeClass('toggle');
        $("footer .footer-column ul.list-unstyled").css('display', 'block');
        $("footer .footer-column h5.toggle").unbind("click");
    }
}

$(document).ready(function () {
    footerToggle();
});
$(window).resize(function () {
    footerToggle();
});


/* Category List - Tree View */
function categoryListTreeView() {
    $(".category-treeview li.category-li").find("ul").parent().prepend("<div class='list-tree'></div>").find("ul").css('display', 'none');

    $(".category-treeview li.category-li.category-active").find("ul").css('display', 'block');
    $(".category-treeview li.category-li.category-active").toggleClass('active');
}

$(document).ready(function () {
    categoryListTreeView();
});


/* Category List - TreeView Toggle */
function categoryListTreeViewToggle() {
    $(".category-treeview li.category-li .list-tree").click(function () {
        $(this).parent().toggleClass('active').find('ul').slideToggle();
    });
}

$(document).ready(function () {
    categoryListTreeViewToggle();
});
// $(document).ready(function(){ menuMore(); });
/* Main Menu - MORE items */

function menuMore() {
    //$(function($){
    var max_items = 14;
    var liItems = $('.navbar-nav > li');
    var remainItems = liItems.slice(max_items, liItems.length);
    remainItems.wrapAll('<li class="dropdown more-menu"><div class="dropdown-menu"><div class="dropdown-inner"><ul class="list-unstyled childs_1">');
    $('.more-menu').prepend('<span>More</span>');
    //});
}

function menuToggle() {
    if ($(window).width() < 992) {
        $("#menu .navbar-collapse > ul > li.dropdown > i").remove(".fa.fa-angle-down");
        $("#menu .navbar-collapse > ul > li.dropdown > a").after("<i class='fa fa-angle-down'></i>");
        $("#menu .navbar-collapse > ul > li.dropdown.more-menu > i").remove(".fa.fa-angle-down");
        $("#menu .navbar-collapse > ul > li.dropdown.more-menu > span").after("<i class='fa fa-angle-down'></i>");
    } else {
        $("#menu .navbar-collapse > ul > li.dropdown > i").remove(".fa.fa-angle-down");
    }

    /* menu item toggle active */
    $("#menu .navbar-collapse> ul li.dropdown > i").click(function () {
        $(this).parent().toggleClass('active').find(".dropdown-menu").first().slideToggle();
    });
}

$(document).ready(function () {
    menuToggle();
});
$(window).resize(function () {
    menuToggle();
});


/* Animate effect on Review Links - Product Page */
$(".product-total-review, .product-write-review").click(function () {
    $('html, body').animate({scrollTop: $(".product-tabs").offset().top}, 1000);
});

//* FilterBox - Responsive Content*/
function optionFilter() {

    if ($(window).width() <= 991) {
        $('#column-left .option-filter-box').appendTo('.row #content .category-list');
        $('#column-right .option-filter-box').appendTo('.row #content .category-list');
    } else {
        $('.row #content .category-list .option-filter-box').appendTo('#column-left .option-filter');
        $('.row #content .category-list .option-filter-box').appendTo('#column-right .option-filter');
    }
}

$(document).ready(function () {
    optionFilter();
});
$(window).resize(function () {
    optionFilter();
});

/*category filter js*/
function columnToggle() {
    if ($(window).width() < 992) {

        $("#column-left .panel-heading").addClass("toggle");
        $("#column-left .list-group").css('display', 'none');
        $("#column-left .panel-default.active .list-group").css('display', 'block');
        $("#column-left .panel-heading.toggle").unbind("click");
        $("#column-left .panel-heading.toggle").click(function () {
            $(this).parent().toggleClass('active').find('.list-group').slideToggle("fast");
        });

        $("#column-left .box-heading").addClass("toggle");
        $("#column-left .products-carousel").css('display', 'none');
        $("#column-left .products-list.active .products-carousel").css('display', 'block');
        $("#column-left .box-heading.toggle").unbind("click");
        $("#column-left .box-heading.toggle").click(function () {
            $(this).parent().toggleClass('active').find('.products-carousel').slideToggle("fast");
        });

    } else {

        $("#column-left .panel-heading").unbind("click");
        $("#column-left .panel-heading").removeClass("toggle");
        $("#column-left .list-group").css('display', 'block');

        $("#column-left .box-heading").unbind("click");
        $("#column-left .box-heading").removeClass("toggle");
        $("#column-left .products-carousel").css('display', 'block');

    }
}

$(document).ready(function () {
    columnToggle();
});
$(window).resize(function () {
    columnToggle();
});

function responsivecolumn() {
    if ($(document).width() <= 991) {
        $('#page > .container > .row > #column-left').appendTo('#page  > .container > .row');
    }
    else if ($(document).width() >= 992) {
        $('#page > .container > .row > #column-left').prependTo('#page  > .container > .row');
    }
}

$(document).ready(function () {
    responsivecolumn();
});

$(window).resize(function () {
    responsivecolumn();
});
/*category filter js end*/