
$(document).ready(function() {
    $('#username').keyup(checkUsername);
    $('#username').change(checkUsername);
});

function checkUsername()
{
    var inputUsername = $('#username').val();
    $.getJSON("/api/check-username",
        {
            username: inputUsername
        },
        function(data) {
            var html = '';
            if (data.isValid) {
                if (data.isAvailable) {
                    html = '<span style="color:green; font-weight:bold;">available</span>';
                } else {
                    html = '<span style="color:red; font-weight:bold;">not available</span>';
                }
            } else {
                html = '<span style="color:red; font-weight:bold">' + data.message + '</span>';
            }
            $('#usernameAvailabilty').html(html);
        }
    );
}
