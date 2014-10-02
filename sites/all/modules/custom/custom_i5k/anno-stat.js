jQuery(document).ready(function ($) {
    $.ajax({
        type: 'GET',
        url: 'sites/all/modules/custom/custom_i5k/anno-stat.json',
        data: "{}",
        dataType: "json",
        success: function (data) {
         var stat = data;
         var str = '';
         str= str + "<font size='3' weight='3'>Total Annotations: " + stat[0].split(":")[1] + "</font><br>";
         str = str + "(over " + stat[1].split(":")[1] + " species)<br>";

         for (var i=0;i<stat[3].length;++i) {
           str = str + "<table><caption>" + stat[3][i].title + "</caption><tr><th>Species</th><th># of annotations</th><th>Last modified</th><th>End date</th></tr>";
             for (var j=0; j<stat[3][i].details.length ; ++j) {
               str = str + "<tr><td><a href='http://i5k.nal.usda.gov/JBrowse-" + stat[3][i].details[j].abbr  + "' target='_blank'>" + stat[3][i].details[j].species
                     + "</a></td><td>" +  stat[3][i].details[j].annotations + "</td><td>" + stat[3][i].details[j].last_modified + "</td><td>" + stat[3][i].details[j].end_date + "</td></tr>";
             }
           str = str + "</table>";
         }
         str = str + "Last update: " + stat[2].substring(12);
         $('#stats').append(str);
        }
    });
});
