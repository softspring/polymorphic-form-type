(function($) {
    function removePolymorphicNode($removeNodeLink)
    {
        var $removeRow = $removeNodeLink.closest('.node-row');

        // custom for this
        var $nodes = $removeRow.parent().find('.node-row');
        var nodes = $nodes.length;

        $removeRow.get(0).dispatchEvent(new Event('remove_polymorphic_node', {bubbles: true}));
        $removeRow.remove();

        // custom for this
        if (nodes-1 <= 1) {
            $nodes.find('.remove_polymorphic_node').hide();
        }
    }

    function getPolymorphicCollectionLastIndex($collection)
    {
        return parseInt($collection.find('.node-row').last().data('index'));
    }

    function addPolymorphicNode ($addNodeLink)
    {
        var $collection = $($addNodeLink.data('collection'));
        var initialIndex = parseInt($collection.attr('data-initial-index'));
        var lastIndex = getPolymorphicCollectionLastIndex($collection);
        var index = isNaN(lastIndex) ? 0 : (lastIndex <= initialIndex ? initialIndex+1 : lastIndex+1);

        // create and process prototype
        var prototype = $addNodeLink.data('prototype');
        var newRow = prototype.replace(new RegExp($addNodeLink.data('prototype-name'), 'g'), index);
        var $newRow = $(newRow);

        // append node to form
        $collection.append($newRow);

        $newRow.get(0).dispatchEvent(new Event('add_polymorphic_node', {bubbles: true}));

        $collection.find('.remove_polymorphic_node').show();
    }

    $('.polymorphic-collection').each(function() {
        var $collection = $(this);
        var lastIndex = getPolymorphicCollectionLastIndex($collection);
        $collection.attr('data-initial-index', isNaN(lastIndex) ? '' : lastIndex);
    });

    $(document).on('click', '.add_polymorphic_node', function(event){
        event.preventDefault();
        addPolymorphicNode($(this));
    });

    $(document).on('click', '.remove_polymorphic_node', function(event){
        event.preventDefault();
        removePolymorphicNode($(this));
    });
})(jQuery);