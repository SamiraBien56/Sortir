$(document).on('change', '#creer_une_sortie_ville', function () {
    let $field = $(this)
    let $villeField = $('#creer_une_sortie_ville')
    let $form = $field.closest('form')
    let target = '#' + $field.attr('id').replace('ville', 'lieu')
    // Les données à envoyer en Ajax
    let data = {}
    data[$villeField.attr('nom')] = $villeField.val()
    data[$field.attr('nom')] = $field.val()
    // On soumet les données
    $.post($form.attr('action'), data).then(function (data) {
        // On récupère le nouveau <select>
        let $input = $(data).find(target)
        // On remplace notre <select> actuel
        $(target).replaceWith($input)
    })
})
