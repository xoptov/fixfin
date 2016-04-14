$(document).ready(function() {
    $(".link-1").hover (
        function() {
           $("#tape-orange").addClass('active');
        },
        function() {
           $("#tape-orange").removeClass('active');
        }
    )

    $(".link-2").hover (
        function() {
           $("#tape-blue").addClass('active');
        },
        function() {
           $("#tape-blue").removeClass('active');
        }
    )   


    $(".link-3").hover (
        function() {
           $("#tape-red").addClass('active');
        },
        function() {
           $("#tape-red").removeClass('active');
        }
    )    


    $(".link-4").hover (
        function() {
           $("#tape-green").addClass('active');
        },
        function() {
           $("#tape-green").removeClass('active');
        }
    )   

    $('.popup').magnificPopup({
        type: 'inline',

        fixedContentPos: true,
        fixedBgPos: true,

        overflowY: 'auto',

        closeBtnInside: true,
        preloader: false,
    
        midClick: true,
        removalDelay: 300,
        mainClass: 'my-mfp-zoom-in'
    });

    $('.popup-youtube').magnificPopup({
      disableOn: 700,
      type: 'iframe',
      mainClass: 'mfp-fade',
      removalDelay: 160,
      preloader: false,

      fixedContentPos: false
    });    
});