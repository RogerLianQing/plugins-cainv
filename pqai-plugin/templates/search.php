<link href="/testsite/wp-content/plugins/pqai-plugin/assets/bootstrap.min.css" rel="stylesheet">
<script src="/testsite/wp-content/plugins/pqai-plugin/assets/bootstrap.min.js"></script>
<style>
    .card {
        margin-top: 20px;
        padding: 10px;
        max-width: 53em;
        border: 1pt solid #CCC;
    }
    .card-body {
        padding: 0;
    }
    .patent-title {
        padding-top: 0;
        margin-top: 0;
    }
    .patent-image {
        border: 1pt solid #ccc;
        width: 155px; 
        height: 210px;
    }
    .patent-image img {
        width: 153px;
        height: 207px;
    }
</style>
<div class="container my-5" style="max-width: 800px">
	<div id="input-pane">
		<div class="d-flex flex-column search-area">
			<div class="d-flex flex-row justify-content-between">
				<h2 class="header">Find Prior Art</h2>
			</div>
			<div class="d-flex flex-column">
				<label class="mb-1">Enter an invention description here:</label>
				<textarea class="form-control" name="search-text" id="search-text-area" rows="4">Passing electric current through a tungsten filament to produce light</textarea>
			</div>
			<div class="mt-2">
				<button onclick="searchPQAI()" id="search-btn" class="btn btn-primary">Search</button>
			</div>
		</div>
	</div>
	<div class="btns">
		<button id='prev_btn' onclick=prev_page() style="display : none;">Previous</button>
		<button id='next_btn' onclick=next_page() style="display : none;">Next</button>
	</div>
	
	<div id="output-pane">
		<!-- results are rendered here -->
	</div>
	
	
</div>

<script>
    var isSearching = false;

	var resultsTotal; 
	var current_page_num = 1;
	var results_per_page = 10;
	
    function searchPQAI() {
		
        var query = document.getElementById('search-text-area').value.trim();
        if (!query) {
            return alert('Please enter text in search box.')
        }

        disableSearchControls();
		console.log(query);
        search(query, function(results) {
			resultsTotal = results.length;
            enableSearchControls();
            render(results);
			current_page('flex');
			var next = document.getElementById('next_btn').style.display = 'flex';
        });
    }

    function disableSearchControls() {
        isSearching = true;
        var searchBtn = document.getElementById('search-btn');
        searchBtn.disabled = true;
        searchBtn.innerHTML = 'Searching...';
        document.getElementById('search-text-area').disabled = true;
        document.querySelector('#output-pane').innerHTML = 'Searching, please wait...';
    }

    function enableSearchControls() {
        isSearching = false;
        document.querySelector('#output-pane').innerHTML = '';
        var searchBtn = document.getElementById('search-btn');
        searchBtn.disabled = false;
        searchBtn.innerHTML = 'Search';
        document.getElementById('search-text-area').disabled = false;
    }

    function search(query, callback) {
        var xhttp = new XMLHttpRequest();
        var url = '/testsite/wp-content/plugins/pqai-plugin/api/search.php?query=';
		/*var url = '/wp-content/plugins/pqai-plugin/api/search.php?query=';*/
        url += encodeURI(query);

        xhttp.onreadystatechange = function() {
            if (xhttp.readyState == XMLHttpRequest.DONE) {
                if (xhttp.status == 200) {
                    if (Array.isArray(xhttp.response)) {
                        
						return callback(xhttp.response);
                    }
                    
					return callback(xhttp.response.results);
                }
                else {
                    alert("An error occurred. Please check your input.");
                    resultsTotal = 0;
					return callback([]);
                }
            }
        };

        xhttp.open('GET', url, true);
        xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhttp.setRequestHeader('Access-Control-Allow-Origin', '*');
        xhttp.responseType = 'json';

        xhttp.send();
    }
	function current_page(mode) {
		var target = Array.from(document.getElementsByClassName(current_page_num.toString()));
		target.forEach(function(item) {
			item.style.display = mode
		});
	}
	
	function next_page() {
		current_page('none');
		current_page_num++;
		if (current_page_num === 2) {
			var prev = document.getElementById('prev_btn').style.display = 'flex'
		}
		if (current_page_num === Math.ceil(resultsTotal/results_per_page)) {
			var next = document.getElementById('next_btn').style.display = 'none'
		}
		current_page('flex');
	}
	
	function prev_page() {
		current_page('none');
		current_page_num--;
		if (current_page_num === 1) {
			var prev = document.getElementById('prev_btn').style.display = 'none'
		}
		if (current_page_num === Math.ceil(resultsTotal/results_per_page - 1)) {
			var next = document.getElementById('next_btn').style.display = 'flex'
		}
		current_page('flex');
	}
	
    function render(results) {
        var target = document.querySelector('#output-pane');
        if (results.length === 0) {
            target.innerHTML = 'No results found.';
            return;
        }
		
        target.innerHTML += '<h3 class="mt-3">Results</h3>';
		let count = 0;
		let class_num = 1;
        for (let i = 0; i < results.length; i++) {
			if (count === results_per_page) {
				count = 0;
				class_num++;
				/**/
			}
            target.innerHTML += _getResultCard(results[i], class_num);
			count++;
			
        };
		
		
		
		
        function _getResultCard(result, class_num) {
            var html = '<div class=' + class_num +' style="display : none;">'
			/*if (index >= current_page_num*results_per_page || index < (current_page_num - 1)*results_per_page) {
				html += 'style="display : none;"';
			} 
			html +=	'>';*/
            html +=     '<div class="d-flex flex-row gap-2">';
            html +=         '<div class="patent-image">';
            html +=             '<img src="'+ result.image +'" alt="' + result.publication_id + '">'; 
            html +=         '</div>';
            html +=         '<div class="card-body">';
            html +=             '<h5 class="patent-title">' + result.title.trim() + '</h5>';
            html +=             '<div class="patent-bib">';
            html +=                 '<div class="patent-owner">';
            html +=                     result.owner;
            html +=                 '</div>';
            html +=                 '<div class="d-flex flex-row patent-meta gap-1">';
            html +=                     '<div class="patent-number">';
            html +=                         '<a href="' + result.www_link + '">' + result.publication_id + '</a>';
            html +=                     '</div>';
            html +=                     '<b>&#xb7;</b>';
            html +=                     '<div class="publication-date">';
            html +=                         'Published ' + _getShortDate(result.publication_date);
            html +=                     '</div>';
            html +=                     '<b>&#xb7;</b>';
            html +=                     '<div class="inventor">';
            html +=                         _getFewerInventors(result.inventors);
            html +=                     '</div>';
            html +=                 '</div>';
            html +=                 '<div class="patent-abstract card-text">'+ result.abstract.trim() +'</div>';
            html +=             '</div>';
            html +=         '</div>';
            html +=     '</div>';
            html += '</div>';
            return html;
        }

        function _getFewerInventors(inventors) {
            if (inventors.length > 1) {
                return inventors[0] + ' et al.'
            }
            return inventors.join('');
        }

        function _getShortDate(date) {
            return date.slice(0, 4);
        }
    }
</script>
