(function($){
  var oldVals={};

  $('#image-strip-medium, #image-strip-large').change(function(){
    var $s = $(this),
      val = $s.val(),
      size = ($s.attr('id').indexOf('medium')!=-1?'#medium_size_':'#large_size_');
    
    if($(size+'w').is(":hidden")){
      $(size+'w').val(oldVals[size]).show();
      $(size.replace('#','label[for="')+'w"]').show();
    }
    if($(size+'h').is(":hidden")){
      $(size+'h').val(oldVals[size]).show();
      $(size.replace('#','label[for="')+'h"]').show();
    }
    if(val==1){
      oldVals[size]=$(size+'h').hide().val();
      $(size+'h').hide().val(9999); 
      $(size.replace('#','label[for="')+'h"]').hide();
    }else if(val==2){
      oldVals[size]=$(size+'w').hide().val();
      $(size+'w').hide().val(9999); 
      $(size.replace('#','label[for="')+'w"]').hide();
      
    }/*else {//val=0

    }*/
  }).trigger('change');

}(jQuery));

