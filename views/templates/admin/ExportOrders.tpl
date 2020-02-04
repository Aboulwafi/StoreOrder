
<div class ="container">
 

    <div class="col-md-2">
        <div class="panel">
            <div class="list-group">
                <button  type="button" class="btn btn-primary list-group-item" onclick="exportTableToCSV('stores.csv')">EXPORT CSV</button>
            </div>
        </div>
    </div>


    <div class="col-md-10">
            <div class="panel" style="overflow: scroll;">
                <table class="table table-striped">
                        <thead>
                            <tr>
                            <th scope="col">CODE ARTICLE</th>
                            <th scope="col">QUANTITE</th>
                            <th scope="col">DATE</th>
                            </tr>
                        </thead>
                        <tbody>
                        {foreach $orders as $order}
                            <tr>
                                <th>{$order.code_article}</th>
                                <td>{$order.quantite}</td>
                                <td>{$order.date}</td>
                            </tr>
                        {/foreach}
                        </tbody>
                 </table>
            </div>
    </div>

</div>


        <!-- Scripts ----------------------------------------------------------- -->


        <script type='text/javascript'>
         {literal}


                            var today = new Date();
                            var dd = today.getDate();
                            var mm = today.getMonth()+1; //January is 0!
                            var yyyy = today.getFullYear();
                            var min = today.getMinutes();
                            var hours = today.getHours();
                            var sec =  today.getSeconds();

                            if(dd<10) {
                                dd = '0'+dd
                            } 

                            if(mm<10) {
                                mm = '0'+mm
                            } 

                            today = dd + '-' + mm + '-' + yyyy + '-'+hours+min+sec;



                    function downloadCSV(csv, filename) {
                            var csvFile;
                            var downloadLink;

                            // CSV file
                            csvFile = new Blob([csv], {type: "text/csv"});

                            // Download link
                            downloadLink = document.createElement("a");

                            // File name
                            downloadLink.download = filename;

                            // Create a link to the file
                            downloadLink.href = window.URL.createObjectURL(csvFile);

                            // Hide download link
                            downloadLink.style.display = "none";

                            // Add the link to DOM
                            document.body.appendChild(downloadLink);

                            // Click download link
                            downloadLink.click();
                }

                    function exportTableToCSV(filename) {
                            var csv = [];
                            var rows = document.querySelectorAll("table tr");
                            
                            for (var i = 1; i < rows.length; i++) {
                                var row = [], cols = rows[i].querySelectorAll("td, th");
                                
                                for (var j = 0; j < cols.length; j++) 
                                    row.push(cols[j].innerText);
                                
                                csv.push(row.join(";"));        
                            }

                            // Download CSV file
                            downloadCSV(csv.join("\n"), today+"-"+filename);
                    }

         {/literal}
         </script>




