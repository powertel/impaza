<!-- <script>
    const jobs = ['fault 1', 'fault 2', 'fault 3', 'fault 4', 'fault 5'];
const assistant = ['John :', 'Martin :', 'lovemore :'];
const assistantLenght = assistant.length;
let assistantIndex = 0;
const langKeys = {};
for (var i = 0; i < jobs.length; i++) {
  var job = jobs[i];
  langKeys[job] = assistant[assistantIndex];
  assistantIndex++
  if (assistantIndex >= assistantLenght) {
    assistantIndex = 0;
  }
}
console.log(JSON.stringify(langKeys));

console.log()
</script> -->

<!--  <script>

  $.ajax({
    url: "/getfaults",
    url: "/getusers",
    success: function( result ) {
      
     var jobs = result;
     console.log(jobs);
     
      const assistant = ['John :', 'Martin :', 'lovemore :'];
      const assistantLenght = assistant.length;
      let assistantIndex = 0;

      const langKeys = {};

      for (var i = 0; i < jobs.length; i++) {

        var job = jobs[i];
        console.log(job)

        langKeys[job.id] = assistant[assistantIndex];
        
        assistantIndex++

        if (assistantIndex >= assistantLenght) {
          assistantIndex = 0;
        }

      }
      console.log(JSON.stringify(langKeys));
    }
  });
</script> -->

<script>
  var ajax1 = $.ajax({ 
  dataType: "json",
  url: "/getfaults",
  success: function(result) {

  }                     
});


var ajax2 = $.ajax({ 
  dataType: "json",
  url: "/getusers",
  success: function(res) {

  }  
});

$.when( ajax1 , ajax2  ).done(function( a1, a2 ) {
      var jobs = a1;
      var assistant = a2;

       const assistantLenght = assistant.length;
       let assistantIndex = 0;
 
       const langKeys = [];

       for (var i = 0; i < jobs.length; i++) {
 
         var job = jobs[i];
  
 
         langKeys[job] = assistant[assistantIndex];
         console.log(langKeys[job])
         
         
         assistantIndex++
 
         if (assistantIndex >= assistantLenght) {
           assistantIndex = 0;
         }
 
       }
       console.log(langKeys);
});
</script>
