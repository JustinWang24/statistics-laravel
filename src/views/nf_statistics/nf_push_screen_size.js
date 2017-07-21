<script type="application/javascript">
    $(document).ready(function(){
        // Todo Check if the indicator exist
        if(document.cookie.length>0 && document.cookie.indexOf('nf_need_screen_size') != -1){
            var screenWidth = window.screen.availWidth;
            var screenHeight = window.screen.availHeight;
            var url = '/nf_push_screen_size?sw='+screenWidth+'&sh='+screenHeight;
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
        }
    });
</script>