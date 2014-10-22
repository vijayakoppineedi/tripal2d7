jQuery(document).ready(function () {
    jQuery.ajax({
        type: 'GET',
        url: 'sites/all/modules/custom/custom_i5k/stat.json',
        data: "",
        dataType: "json",
        success: function(data) {		  
            // extract types and their subtypes
            var types = [];
            for (var i=0; i<data[3][0].details.length ; ++i) {
                for (var j=0; j<data[3][0].details[i].ftypes.length; ++j) {
                    // type, such as gene. Only types(solumn) with num > 0 are visible
                    if (parseInt(data[3][0].details[i].ftypes[j].num) > 0) {
                        var type_e = data[3][0].details[i].ftypes[j].name;
                        if (jQuery.inArray(type_e, types) == -1) {
                            types.push(type_e);
                        }
                        // subtype, such as gene_mRNA
                        for (var k=0; k<data[3][0].details[i].ftypes[j].subtypes.length; ++k) {
                            var subtype_e = data[3][0].details[i].ftypes[j].subtypes[k].name;
                            if (jQuery.inArray(subtype_e, types) == -1) {
                                types.push(subtype_e);
                            }
                        }
                    }
                }
            }
            types.sort();
            //for (var i=0; i<types.length; ++i)
            //    console.log(types[i]);

            // establish index mapping between table header and its index
            var header_map = {
                "species": 1,
                "total": 2,
                "last_modified": (types.length + 3),
            };

            // initialze the table
            var str = '';
            str += "Total Annotations: " + data[0].split(":")[1] + " (" + data[1].split(":")[1] + " species)";
            str += "<table id='myTable' class='tablesorter'><thead><tr>";
            str += "<th>Species</th><th>total</th>";
            for (var i=0; i<types.length; ++i) {
                header_map[types[i]] = i + 3;
                str += "<th>" + types[i] + "</th>";
            }
            str += "<th class=\"{sorter: \'date\'}\">last modified</th></tr></thead>";
            str += "<tbody>";
            for (var j=0; j<data[3][0].details.length ; ++j) {
                str += "<tr><td><a href='https://i5k.nal.usda.gov/" + data[3][0].details[j].species.replace(" ", "_") + "' target='_blank'>" 
                    + data[3][0].details[j].species + "</a></td>";
                str += "<td>" + data[3][0].details[j].annotations + "</td>";
                for (var k=0; k<types.length; ++k) {
                    str += "<td>0</td>";
                }
                str += "<td>" + data[3][0].details[j].last_modified + "</td></tr>";
            }
            str += "</tbody></table>";

            str += "Last Update: " + data[2].substring(12);

            jQuery('#stat').append(str);

            // fill in values in the table
            var row_index, col_index, selector, value;
            for (var i=0; i<data[3][0].details.length ; ++i) {
                row_index = i+1;
                for (var j=0; j<data[3][0].details[i].ftypes.length; ++j) { // type
                    var tname = data[3][0].details[i].ftypes[j].name;
                    if (jQuery.inArray(tname, types) != -1) { // Only visible columns(types) will be filled. 
                        col_index = header_map[tname];
                        selector = "#myTable tr:nth-child(" + row_index + ") td:nth-child(" + col_index + ")";
                        value = data[3][0].details[i].ftypes[j].num;
                        jQuery(selector).html(value);
                        //console.log(data[3][0].details[i].ftypes[j].name, data[3][0].details[i].ftypes[j].num); // type
                        for (var k=0; k<data[3][0].details[i].ftypes[j].subtypes.length; ++k) { // subtype
                            var tsubname = data[3][0].details[i].ftypes[j].subtypes[k].name;
                            col_index = header_map[tsubname];
                            selector = "#myTable tr:nth-child(" + row_index + ") td:nth-child(" + col_index + ")";
                            value = data[3][0].details[i].ftypes[j].subtypes[k].num;
                            jQuery(selector).html(value);
                            //console.log(data[3][0].details[i].ftypes[j].subtypes[k].name, data[3][0].details[i].ftypes[j].subtypes[k].num); // subtype
                        }
                    }
                }
            }

            jQuery.tablesorter.addParser({
                id: 'date',
                is: function(s) { 
                    return false; // return false so this parser is not auto detected
                },
                format: function(s) { 
                    // format your data for normalization
                    var d = new Date(s);
                    return d.toISOString();
                },
                type: 'text' // either numeric or text
            });
           
            jQuery("#myTable").tablesorter({
                sortList: [[1,1]],   // initializing by sorting on the first column in descending order
            });
        }
    });
});
