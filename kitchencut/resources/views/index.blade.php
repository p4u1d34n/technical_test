<html>

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha512-mSYUmp1HYZDFaVKK//63EcZq4iFWFjxSL+Z3T/aCt4IO9Cejm03q3NKKYN6pFQzY0SBOr8h+eCIAZHPXcpZaNw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid-theme.css" integrity="sha512-fz1HF9fyPeFY4eK3GvDxWRjAnpUdoCZq+c96Gnt4kX4SCN/+r/iPyUiYE9iPMSrkXMZoqZ00YHPGy7SzdxYImA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="/public/css/index.css" />
</head>

<body>

    <div class="container">

        <div class="row">
            <div class="col-sm">
                <h3>Range Date, Status and Location Filter</h3>
                <small>Returns invoice headers and total value</small>

                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <input id="from" type="text" class="form-control datepicker" placeholder="From date">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <input id="to" type="text" class="form-control datepicker" placeholder="To date">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label>Location</label>
                            <select type="checkbox" class="form-input" id="lid">
                                <option value="">None</option>
                                @foreach($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col" 6->
                        <div class="form-group">
                            <label>Status</label>
                            <select type="checkbox" class="form-input" id="status">
                                <option value="">None</option>
                                @foreach($states as $state)
                                <option value="{{ $state->status }}">{{ $state->status }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div onclick="getFilter()" class="btn btn-primary">Filter</div>

            </div>
            <div class="col-sm">
                <h3>Location ID Selection</h3>
                <small>Returns the value sum of the Invoices grouped by status</small>

                <div class="form-group ">
                    <label>Location</label>
                    <select type="checkbox" class="form-input" name="location_id" id="location_id">
                        <option option value="">None</option>
                        @foreach($locations as $location)
                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div onclick="getLocation()" class="btn btn-primary">Choose</div>

            </div>
        </div>

        <div class="row colview">
            <h3>Filtered Data:</h3>
            <div class="col-sm">
                <div id="table"></div>
            </div>
        </div>

        <div class="row colview">
            <h3>Aggregated Data:</h3>
            <div class="col-sm">
                <div id="table_two"></div>
            </div>
        </div>

    </div>

</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha512-T/tUfKSV1bihCnd+MxKD0Hm1uBBroVYBOYSk1knyvQ9VyZJpc/ALb4P0r6ubwVPSGB2GvjeoMAJJImBG12TiaQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid.min.js" integrity="sha512-blBYtuTn9yEyWYuKLh8Faml5tT/5YPG0ir9XEABu5YCj7VGr2nb21WPFT9pnP4fcC3y0sSxJR1JqFTfTALGuPQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    $.fn.datepicker.defaults.format = "yyyy-mm-dd";
    $('.datepicker').datepicker({

    });

    function getFilter() {
        var from = document.querySelector('#from').value;
        var to = document.querySelector('#to').value;
        var status = document.querySelector('#status').value;
        var location_id = document.querySelector('#lid').value;
        var el = document.querySelector('#table')
        api('POST', '/public/filter', {
            from,
            to,
            location_id,
            status
        },el);
    }

    function api(type, url, data, el) {
        $.ajax({
            type,
            url,
            data,
            success: function(data) {
                jsonList(el, data);
            }
        });
    }

    function getLocation() {
        var location_id = document.querySelector('#location_id').value;
        var el = document.querySelector('#table')
        api('GET', '/public/location/' + location_id, {},el);
    }

    window.onload = function() {
        getAggregate()
    }

    function getAggregate() {
        var el = document.querySelector('#table_two');
        api('GET', '/public/aggregate', {},el);
    }

    // quick and dirty table maker
    function jsonList(el, json) {
        el.innerHTML = "";
        // HEADERS

        let keys = Object.keys(json[0]);
        let row = document.createElement('div');
        row.classList.add('row');
        row.classList.add('head');
        keys.forEach(key => {
            var col = document.createElement('div');
            col.classList.add('col');
            col.append(key);
            row.appendChild(col);
        })
        el.appendChild(row);

        // TABLE CONTENT
        json.forEach((value, index) => {
            var row = document.createElement('div');
            row.classList.add('row');
            let keys = Object.keys(value);
            keys.forEach(key => {
                var col = document.createElement('div');
                col.classList.add('col');
                if (typeof key === 'object' && key !== null) {
                    col.append(JSON.stringify(value[key]));
                } else {
                    col.append(value[key]);
                }
                row.appendChild(col);
            })
            el.appendChild(row);
        })

    }
</script>

</html>