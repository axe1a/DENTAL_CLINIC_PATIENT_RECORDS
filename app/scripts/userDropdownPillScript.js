$('#userPill').on('click', function(e) {
    e.stopPropagation();
    $('#userDropdown').toggle();
});
$(document).on('click', () => $('#userDropdown').hide());