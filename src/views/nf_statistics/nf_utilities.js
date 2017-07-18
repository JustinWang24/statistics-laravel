<script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
<script type="application/javascript">
    $(document).ready(function(){
        // Todo Check if the indicator exist
        navigator.geolocation.getCurrentPosition(function(position) {
            var url = '/nf_push_geo_location?lat='+position.coords.latitude+'&lng='+position.coords.longitude;
            /**
             * I assume you are using jQuery. If not, feel free use any lib you like to make this ajax call happen.
             * 这里使用 jQuery. 也可以用任何你使用的 js 库来执行这个 ajax 请求到上面定义的 URL.
             */
            $.get(url,function(res){
                if(res == 'success'){
                    // Remove the cookie when success
                    var date=new Date();
                    date.setTime(date.getTime()-10000);
                    document.cookie='nf_need_screen_size=0;expires='+date.toGMTString();
                }
            });
        });
    });
</script>