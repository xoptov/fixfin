var app = {};

// Tabs on dashboard page
$('.nav-tabs a').on('click', function(e){
    e.preventDefault();
    $(this).tab('show');
});