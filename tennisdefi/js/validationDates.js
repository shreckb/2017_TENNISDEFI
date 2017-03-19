// Validates that the input string is a valid date formatted as "dd/mm/yyyy"
function isValidDate(input){
  var reg = /(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)\d\d/;
  if (input.match(reg))
    return true;
  else 
    return false;
  
}