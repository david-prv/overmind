var dropHistory = {
    stack: [],
    counter: -1,
    add: function(item){
        this.stack[++this.counter] = item;

        // delete anything forward of the counter
        this.stack.splice(this.counter + 1);
    },
    undo: function(){
        this.deleteDrop(this.stack.pop());
        counter--;
    },
    deleteDrop: function(item){
        let el = document.getElementById(item);
        if(el !== undefined && el !== null) {
            el.remove();
        }
    }
};

$('.drag').draggable({
    appendTo: 'body',
    helper: 'clone',
    hoverClass: 'hover'
});

$('#dropzone').droppable({
    activeClass: 'active',
    hoverClass: 'hover',
    accept: ":not(.ui-sortable-helper)", // Reject clones generated by sortable
    drop: function (e, ui) {
        let price = ui.draggable[0].getAttribute("data-price");
        let identifier = Date.now();

        dropHistory.add(identifier);

        var $el = $('<div class="drop-item" data-text="' + ui.draggable.text().trim() + '" data-price="' + price + '" id="' +
            identifier + '"><details><summary>' + ui.draggable.text() + '</summary><div>' +
            '<form>\n' +
            '  <div class="form-group row">\n' +
            '    <label for="staticEmail" class="col-sm-2 col-form-label">Price</label>\n' +
            '    <div class="col-sm-10">\n' +
            '      <input type="text" readonly class="form-control-plaintext" id="staticPrice" value="' + price + '">\n' +
            '    </div>\n' +
            '  </div>\n' +
            '  <div class="form-group row">\n' +
            '    <label for="inputPassword" class="col-sm-2 col-form-label">Comment</label>\n' +
            '    <div class="col-sm-10">\n' +
            '      <input type="text" class="form-control" id="inputComment-' + identifier + '" placeholder="Comment">\n' +
            '    </div>\n' +
            '  </div>\n' +
            '</form>' +
            '</details></div>');
        //$el.append($('<button type="button" class="btn btn-default btn-xs remove"><span class="glyphicon glyphicon-trash"></span></button>').click(function () { $(this).parent().detach(); }));
        $(this).append($el);
    }
}).sortable({
    items: '.drop-item',
    sort: function() {
        // gets added unintentionally by droppable interacting with sortable
        // using connectWithSortable fixes this, but doesn't allow you to customize active/hoverClass options
        $( this ).removeClass( "active" );
    }
});