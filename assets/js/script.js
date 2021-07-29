jQuery('#requestFilter').on('change', function() {
  var value=this.value;
  var url=window.location.href;
  if(url.indexOf('?')>-1){
    if(url.indexOf('sort')>-1){
      url=decodeURIComponent(url.replace(/\+/g, ' '));
      var temp=url.split( '?' );
      var newquery=temp[1].split( '&' ).filter( p => !p.startsWith('sort=' ) ).join( '&' );
      url= encodeURI(temp[0]+'?'+newquery);
        url=url+'&'+'sort'+'='+value;
      }else{
        url=url+'&'+'sort'+'='+value;
      }
    }else{
       url=url+'?'+'sort'+'='+value;
    }
  window.location=url;
});

