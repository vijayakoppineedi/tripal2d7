jQuery(document).ready(function ($) {
    $.ajax({ 
        type: 'GET', 
        url: 'sites/all/modules/custom/custom_i5k/anno-stat.json', 
        data: "{}",
        dataType: "json",
        success: function (data) { 
         var stat = data;
         var str = '';
	 str= str + "Total Annotations: " + stat[0].split(":")[1] + "<br>";
	 str = str + "(over " + stat[1].split(":")[1] + " species)<br>";

         for (var i=0;i<stat[3].length;++i) {    
           str = str + "<table><caption>" + stat[3][i].title + "</caption><tr><th>Species</th><th># of annotations</th><th>Last modified</th></tr>";
             for (var j=0; j<stat[3][i].details.length && j<3; ++j) {
                  //stat[3][i].details[j].end_date
               str = str + "<tr><td><a href='http://i5k.nal.usda.gov/JBrowse-" + stat[3][i].details[j].abbr  + "' target='_blank'>" + stat[3][i].details[j].species
                     + "</a></td><td>" +  stat[3][i].details[j].annotations + "</td><td>" + stat[3][i].details[j].last_modified + "</td></tr>";
             }
           str = str + "</table>";      
         }
         $('#stats').append(str);
        }
    });
});

