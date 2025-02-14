$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
    }
});  

$("#generate").click(
    function() {
        $(".loader").css({
            "display":"flex"
        });
        $.ajax({
            url: "/fill-table",
            type: "POST",
            error(err){
                $(".loader").css({
                    "display":"none"
                });
                alert('Error: ' + JSON.stringify(err))
            },
            success(data){
                $(".loader").css({
                    "display":"none"
                });
                alert("Записи созданы")
                location.reload();
            }
        })
    }
)


$("#truncate").click(
    function() {
        $.ajax({
            url: "/truncate",
            type: "POST",
            error(err){
                alert('Error: ' + JSON.stringify(err))
            },
            success(data){
                alert("Записи удалены")
                location.reload();
            }
        })
    }
)

$("#gsheet_url").change(
    function(){
        let url = $(this).val();
        $.ajax({
            url: "/change-url",
            type: "POST",
            data:{
                url
            },
            error(err){
                alert('Error: ' + JSON.stringify(err))
            },
            success(data){
                console.log("Ссылка изменена")
            }
        })
    }
)