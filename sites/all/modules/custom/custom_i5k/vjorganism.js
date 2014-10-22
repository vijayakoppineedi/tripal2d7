var species = 'anogla';
jQuery(document).ready(function () {
    jQuery.ajax({
        type: 'GET',
        url: 'sites/all/modules/custom/custom_i5k/stat.json',
        data: "",
        dataType: "json",
        success: function(data) {
		   var str = '';
            for (var i=0; i<data[3][0].details.length ; ++i) {
                if (data[3][0].details[i].abbr == species) {
                    for (var j=0; j<data[3][0].details[i].ftypes.length; ++j) {
                        // type, such as gene. Only types(solumn) with num > 0 are visible
                        if (parseInt(data[3][0].details[i].ftypes[j].num) > 0) {
                            var tname = data[3][0].details[i].ftypes[j].name;
                            str += "<tr><th colspan='2'>" + tname + "</th><td>" + data[3][0].details[i].ftypes[j].num + "</td></tr>";
                            // subtype, such as gene_mRNA
                            var subtype_num = data[3][0].details[i].ftypes[j].subtypes.length;
                            for (var k=0; k<subtype_num; ++k) {
                                var subname = data[3][0].details[i].ftypes[j].subtypes[k].name.replace(tname, "");
                                subname = subname.replace("_", "");
                                str += "<tr><th></th><th>" + subname + "</th><td>" + data[3][0].details[i].ftypes[j].subtypes[k].num + "</td></tr>";
                            }
                        }
                    }
                    break;
                }
            }
			
            if (str != '') {
                jQuery('#annotation').append(str);
            }
            else { // no annotation, hide the table
                jQuery('#annotation').hide();
            }
        }
    });
});
