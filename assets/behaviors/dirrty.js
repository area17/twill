a17cms.Behaviors.dirrty = function(form) {
    $(window).load(function() {
        form.dirrty({
            preventLeaving: false
        }).on("dirty", function() {
            console.log('dirty');
            [].forEach.call(form.find('[data-toggle-on-change]'), function(el) {
                $(el).toggle();
            });
        }).on("clean", function() {
            console.log('clean');
            [].forEach.call(form.find('[data-toggle-on-change]'), function(el) {
                $(el).toggle();
            });
        });
    });
};