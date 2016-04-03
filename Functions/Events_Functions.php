<?php
function EWD_FEUPHRS_Get_Event_Link($Event_ID) {
	global $wpdb;
	$events_table = 'wp_em_events';
	$slug = $wpdb->get_var("SELECT event_slug from $events_table WHERE event_id = $Event_ID");
	return site_url().'/?event=' . $slug;
}

function EWD_FEUPHRS_Event_Selector() {
	global $wpdb;
	$events_table = 'wp_em_events';
	$sql =  "SELECT event_id, event_name, ";
	$sql .= "MAX(event_start_date) AS event_start_date ";
	$sql .= "FROM $events_table GROUP BY event_name ";
	$sql .= "ORDER BY event_start_date DESC";
	$events = json_encode($wpdb->get_results($sql));
	return <<<EOT



	<style>

	.ewd-feup-autofill {

		background: white;

		border: 1px solid #ccc;

		display: none;

		max-height: 200px;

		overflow-y: scroll;

		position: absolute;

		min-width: 200px;

		z-index: 10000;

	}

	.ewd-feup-autofill div {

		border-bottom: 1px solid #ccc;

		cursor: pointer;

		font-size: 10px;

		max-width: 100%;

		padding: 0 5px;

	}

	.ewd-feup-autofill div:hover {

		background: #0078E7;

		color: white;

	}

	</style>



	<script type="text/javascript">

	var events = $events;



	function handleInput() {

		var search = this.context.value.toLowerCase(),

			visible = false;



		this.idInput.value = '';



		for (var i=0; i < this.list.children.length; i++) {

			if (this.list.children[i].innerHTML.toLowerCase().indexOf(search) != -1) {

				this.list.children[i].style.display = 'block';

				visible = true;

			} else {

				this.list.children[i].style.display = 'none';

			}

		}



		if (!visible) {

			this.list.style.display = 'none';

		} else  {

			this.list.style.display = 'block';

		}

	}



	function handleDocumentClick(e) {

		if (e.target != this.context &&

			e.target != this.list) {

			this.list.style.display = 'none';

		}

	}



	function handleItemClicked(e) {

		this.idInput.value = e.target.__event_id;

		this.context.value = e.target.innerHTML;

		this.list.style.display = 'none';

	}



	function EventSelector(context) {

		this.idInput = document.createElement('input');

		this.idInput.type = 'hidden';

		this.idInput.name = 'ewd-feup-event-id';

		context.parentNode.insertBefore(this.idInput, context);



		this.list = document.createElement('div');

		this.list.className = 'ewd-feup-autofill';

		this.list.style.top = (context.offsetTop + context.offsetHeight) + 'px';

		this.list.style.left = context.offsetLeft + 'px';

		this.list.style.maxWidth = (context.offsetWidth - 2) + 'px';

		this.list.width = context.width;

		context.parentNode.appendChild(this.list);



		for (var i in events) {

			var item = document.createElement('div');

			item.innerHTML = events[i].event_name;

			item.addEventListener('click', handleItemClicked.bind(this));

			item.__event_id = events[i].event_id;

			this.list.appendChild(item);

		}



		context.autocomplete = 'off';

		context.addEventListener('input', handleInput.bind(this));

		document.addEventListener('click', handleDocumentClick.bind(this));



		this.context = context;

	}



	var selectors = document.getElementsByClassName('ewd-feup-event-selector');



	for (var i=0; i < selectors.length; i++) {

		console.log(new EventSelector(selectors[i]));

	}

	</script>

EOT;

}
