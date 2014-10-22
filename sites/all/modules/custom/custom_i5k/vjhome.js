jQuery(document).ready(function () {
jQuery.support.cors = true;
    jQuery.ajax({
        type: 'GET',
        url: 'sites/all/modules/custom/custom_i5k/stat.json',
        		
        data: "",
        dataType: "json",
        success: function(data) {		
		 jQuery('#total-annotations').html(data[0].split(":")[1]);
            jQuery('#over-species').html(data[1].split(":")[1]);
            var str = '';
            var count = data[3][0].details.length;
            if (count > 5) { count=5;}
            for (var i=0; i<count ; ++i) {
                var sname = data[3][0].details[i].species;
                var snum = data[3][0].details[i].annotations;
                var sdate = data[3][0].details[i].last_modified;
                str += "<tr><td><a href='/"+ sname.replace(" ", "_") + "' target='_blank'>" + sname + "</a></td>"
                    + "<td>" + snum + "</td><td>" + sdate + "</td></tr>;"
            }
            if (str != '') {
                jQuery('#recently-annotations').append(str);
            }
            else { // no annotation, hide the table
                jQuery('#recently-annotations').hide();
            }
        }
    });
});
