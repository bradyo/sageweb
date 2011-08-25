$(document).ready(function() {
    // highlight top menu when submenu is hovered
    $(".subMenuBlock").hover(
        function() {
            var topAnchor = $(this).closest('.menuItem');
            topAnchor.children("a").addClass('dropdownHighlight');
        },
        function () {
            var topAnchor = $(this).closest('.menuItem');
            topAnchor.children("a").removeClass('dropdownHighlight');
        }
    );

    // add tooltips to top links
    $("#lifespanDbLink").tooltip({
        position: "bottom center",
        opacity: 0.95
    });
    $("#pathwayDbLink").tooltip({
        position: "bottom center",
        opacity: 0.95
    });
    $("#yodaLink").tooltip({
        position: "bottom center",
        opacity: 0.95
    });
    $("#kaeberleinLabLink").tooltip({
        position: "bottom center",
        opacity: 0.95
    });
    $("#igagLink").tooltip({
        position: "bottom center",
        opacity: 0.95
    });

    // change the href to postOverlay for overlay when js is enabled
    $("#addPost").attr('href', '#postOverlay');
	$("#addPost").fancybox({
        'transitionIn': 'none',
		'transitionOut': 'none',
		'speedIn': 0,
		'speedOut': 0,
        'changeSpeed': 0,
        'overlayOpacity': .3,
        'overlayColor': '#000',
		'hideOnContentClick': false
	});
});