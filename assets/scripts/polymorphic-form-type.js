(function($) {
/* ************************************************************************************************************* *
 * EVENT LISTENERS
 * ************************************************************************************************************* */

    $(document).on('click', '.polymorphic-add-button', function(event){
        event.preventDefault();
        var $addNodeLink = $(this),
            addNodeLink = this;
        var $collection = $($addNodeLink.data('collection'));
        var prototypeName = $addNodeLink.data('prototype-name');
        var prototype = $addNodeLink.data('prototype');
        document.dispatchEvent(new CustomEvent('cms.module.add.before', { "detail": {"module":newModule } }));
        const newModule = addPolymorphicNode($collection, prototypeName, prototype);
        document.dispatchEvent(new CustomEvent('cms.module.add', { "detail": {"module":newModule } }));
    });

    $(document).on('click', '.polymorphic-remove-node-button', function(event){
        event.preventDefault();
        const nodeButton = this,
              newModule = nodeButton.closest('.polymorphic-node-row'),
              $nodeRow = $(event.target).closest('.polymorphic-node-row'),
              $collection = $nodeRow.closest('.polymorphic-collection-widget');
        document.dispatchEvent(new CustomEvent('cms.module.remove.before', { "detail": {"module":newModule } }));
        removePolymorphicNodeRow($collection, $nodeRow);
        document.dispatchEvent(new CustomEvent('cms.module.remove', { "detail": {"module":newModule } }));
    });

    $(document).on('click', '.polymorphic-down-node-button', function(event){
        event.preventDefault();
        const nodeButton = this,
              newModule = nodeButton.closest('.polymorphic-node-row'),
              $nodeRow = $(event.target).closest('.polymorphic-node-row'),
              $collection = $nodeRow.closest('.polymorphic-collection-widget');
        document.dispatchEvent(new CustomEvent('cms.module.move.down.before', { "detail": {"module":newModule } }));
        moveDownNode($collection, $nodeRow);
        document.dispatchEvent(new CustomEvent('cms.module.move.down', { "detail": {"module":newModule } }));
    })

    $(document).on('click', '.polymorphic-up-node-button', function(event){
        event.preventDefault();
        const nodeButton = this,
              newModule = nodeButton.closest('.polymorphic-node-row'),
              $nodeRow = $(event.target).closest('.polymorphic-node-row'),
              $collection = $nodeRow.closest('.polymorphic-collection-widget');
        document.dispatchEvent(new CustomEvent('cms.module.move.up.before', { "detail": {"module":newModule } }));
        moveUpNode($collection, $nodeRow);
        document.dispatchEvent(new CustomEvent('cms.module.move.up', { "detail": {"module":newModule } }));
    });

    $(document).on('change', '.polymorphic-node-row input', function(event){
        // store value to prevent loosing it on moving
        var $target = $(event.target);
        $target.attr('value', $target.val());
    });

/* ************************************************************************************************************* *
 * ACTIONS
 * ************************************************************************************************************* */

    function addPolymorphicNode ($collection, prototypeName, prototype)
    {
        var lastIndex = getPolymorphicCollectionLastIndex($collection);
        var index = isNaN(lastIndex) ? 0 : lastIndex+1;

        // create and process prototype
        var newRow = prototype.replace(new RegExp(prototypeName, 'g'), index);
        var $newRow = $(newRow);
        // append node to form
        $collection.append($newRow);

        $newRow.get(0).dispatchEvent(new Event('add_polymorphic_node', {bubbles: true}));

        // element which needs to be scrolled to
        let id = $newRow.attr('id'),
            element = document.querySelector("#"+id)
        // scroll to element
        element.scrollIntoView();

        updateCollectionButtons($collection);
        return element;
    }

    function moveUpNode($collection, $nodeRow)
    {
        var $prevNodeRow = $nodeRow.prev('.polymorphic-node-row');

        if ($prevNodeRow.length) {
            $nodeRow.insertBefore($prevNodeRow);
            modifyIndexes($nodeRow, -1);
            modifyIndexes($prevNodeRow, +1);
        }

        updateCollectionButtons($collection);
    }

    function moveDownNode($collection, $nodeRow)
    {
        var $nextNodeRow = $nodeRow.next('.polymorphic-node-row');

        if ($nextNodeRow.length) {
            $nodeRow.insertAfter($nextNodeRow);
            modifyIndexes($nodeRow, +1);
            modifyIndexes($nextNodeRow, -1);
        }

        updateCollectionButtons($collection);
    }

    function removePolymorphicNodeRow($collection, $nodeRow)
    {
        $nodeRow.nextAll('.polymorphic-node-row').each(function (i, nextElement) {
            var $nextElement = $(nextElement);
            modifyIndexes($nextElement, -1);
        })

        $nodeRow.get(0).dispatchEvent(new Event('removed_polymorphic_node', {bubbles: true}));
        $nodeRow.remove();

        updateCollectionButtons($collection);
    }

    function getPolymorphicCollectionLastIndex($collection)
    {
        return parseInt($collection.children('.polymorphic-node-row').last().attr('data-index')); //NOT USE .data('index')
    }

    // init
    $('.polymorphic-collection-widget').each(function() {
        var $collection = $(this);
        updateCollectionButtons($collection);
    });

/* ************************************************************************************************************* *
 * UTILS
 * ************************************************************************************************************* */

    function updateCollectionButtons($collection)
    {
        var $nodeRows = $collection.children('.polymorphic-node-row');

        $nodeRows.each(function (i, nodeRow) {
            var $nodeRow = $(nodeRow);
            var id = $nodeRow.attr('id');
            var index = parseInt($nodeRow.attr('data-index'));
            var lastIndex = $nodeRows.length - 1;

            if (index===0) {
                $('#'+id+'_up_node_button').hide();
            } else {
                $('#'+id+'_up_node_button').show();
            }

            if (index === lastIndex) {
                $('#'+id+'_down_node_button').hide();
            } else {
                $('#'+id+'_down_node_button').show();
            }
        });
    }

    function modifyIndexes($rowElement, increment) {
        var oldIndex = parseInt($rowElement.attr('data-index'));
        var newIndex = oldIndex + increment;
        $rowElement.attr('data-index', newIndex);
        $rowElement.find('#'+$rowElement.attr('id')+'_order_index').html(newIndex);

        var oldRowId = $rowElement.attr('id');
        $rowElement.attr('id', replaceLastOccurence($rowElement.attr('id'), '_'+oldIndex, '_'+newIndex));
        var newRowId = $rowElement.attr('id');

        var oldRowFullName = $rowElement.attr('data-full-name');
        $rowElement.attr('data-full-name', replaceLastOccurence($rowElement.attr('data-full-name'), '['+oldIndex+']', '['+newIndex+']'));
        var newRowFullName = $rowElement.attr('data-full-name');

        $rowElement.html($rowElement[0].innerHTML.replaceAll(oldRowId, newRowId).replaceAll(oldRowFullName, newRowFullName));
    }

    function replaceLastOccurence(text, search, replace) {
        var lastIndex = text.lastIndexOf(search);
        return text.substr(0, lastIndex) + text.substr(lastIndex).replace(search, replace);
    }
})(jQuery);
