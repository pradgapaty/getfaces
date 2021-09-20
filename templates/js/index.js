function like(photoId, iterator, ipAddress)
{
    console.log(photoId);
    $.ajax(
        {
            type: 'POST',
            data: {
                "action" : "like",
                "ip" : ipAddress,
                "photoId" : photoId},
            url: 'Helpers/likePost.php',
            dataType: 'json',
            cache: false,

            success: function (response, status, xhr) {
                if (response) {
                    window.alert(response);
                } else {
                    var locatorLike = 'likePhoto_' + iterator;
                    var locatorDislike = 'dislikePhoto_' + iterator;
                    document.getElementById(locatorLike).style.display = 'none';
                    document.getElementById(locatorDislike).style.display = 'none';
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
                window.alert("Cannot like photo! Something wrong . . .");
            }
        }
    );
}

function dislike(photoId, iterator, ipAddress)
{
    $.ajax(
        {
            type: 'POST',
            data: {
                "action" : "dislike",
                "ip" : ipAddress,
                "photoId" : photoId},
            url: 'Helpers/likePost.php',
            dataType: 'json',
            cache: false,

            success: function (response, status, xhr) {
                if (response) {
                    window.alert(response);
                } else {
                    var locatorLike = 'likePhoto_' + iterator;
                    var locatorDislike = 'dislikePhoto_' + iterator;
                    document.getElementById(locatorLike).style.display = 'none';
                    document.getElementById(locatorDislike).style.display = 'none';
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                 console.log(jqXHR);
                 console.log(textStatus);
                 console.log(errorThrown);
                 window.alert("Cannot dislike photo! Something wrong . . .");
            }
        }
    );
}