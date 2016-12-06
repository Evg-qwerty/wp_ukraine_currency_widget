<?php
/**
 * Plugin Name: Ukraine currency
 * Description: A widget that displays ukraine currency.
 * Version: 0.1
 * Author: Evgeniy Lukovsckiy
 * Author URI:
 */


add_action( 'widgets_init', 'ukraine_currency' );


function ukraine_currency() {
	register_widget( 'Ukraine_currency' );
}

class Ukraine_currency extends WP_Widget {

	function Ukraine_currency() {
		$widget_ops = array( 'classname' => 'ukraine_currency', 'description' => __('Виджет выводит курс гривни ') );

		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'ukraine-currency-widget' );

		$this->WP_Widget( 'ukraine-currency-widget', __('Виджет курса гривни'), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );

		//Our variables from the widget settings.
		$title = apply_filters('widget_title', $instance['title'] );
		$show_nb = isset( $instance['show_nb'] ) ? (bool) $instance['show_nb'] : false;
		$show_pb = isset( $instance['show_pb'] ) ? (bool) $instance['show_pb'] : false;


		echo $before_widget;

		// Title
		if ( $title )
			echo $before_title . $title . $after_title;

		// Нацбанк
		if ($show_nb) {
			$usd        = file_get_contents( "https://bank.gov.ua/NBUStatService/v1/statdirectory/exchange?json&valcode=USD" );
			$eur        = file_get_contents( "https://bank.gov.ua/NBUStatService/v1/statdirectory/exchange?json&valcode=EUR" );
			$nacbankUsd = json_decode( $usd );
			$nacbankEur = json_decode( $eur );
			echo "Нацбанк<br>";
			echo "USD: " . round( $nacbankUsd[0]->rate, 2 ) . "<br>";
			echo "EUR: " . round( $nacbankEur[0]->rate, 2 ) . "<br><br>";
		}

		// Приват
		if( $show_pb ) {
			$private = file_get_contents( "https://api.privatbank.ua/p24api/pubinfo?json&exchange&coursid=5" );
			$privateResult = json_decode( $private );

			echo "Приват банк<br>";
			echo "USD: " . round( $privateResult[2]->sale, 2 ) . "<br>";
			echo "EUR: " . round( $privateResult[0]->sale, 2 ) . "<br><br>";
		}

		echo $after_widget;
	}

	//Update the widget
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['show_pb'] = isset( $new_instance['show_pb'] ) ? (bool) $new_instance['show_pb'] : false;
		$instance['show_nb'] = isset( $new_instance['show_nb'] ) ? (bool) $new_instance['show_nb'] : false;
		return $instance;
	}

	function form( $instance ) {

		$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$show_pb = isset( $instance['show_pb'] ) ? (bool) $instance['show_pb'] : false;
		$show_nb = isset( $instance['show_nb'] ) ? (bool) $instance['show_nb'] : false;

		// Title
		$titleInput = 'Заголовок:';
		$idInput = $this->get_field_id('title');
		$nameInput = $this->get_field_name('title');

		echo "<p>
				<label for='$idInput'>$titleInput</label>
				<input id='$idInput' name='$nameInput' value='$title' style='width: 100%'>
			</p>";

		// NacBank
		$idShowNb = $this->get_field_id('show_nb');
		$nameInputCheckNb = $this->get_field_name('show_nb');
		$titleInputCheckNb = __('Нацбанк', 'ukraine_currency');
		$checkedNb = checked( $show_nb, true, false );

		echo "<p>
				<input id='$idShowNb' class='checkbox' type='checkbox' $checkedNb name='$nameInputCheckNb'>
				<label for='$idShowNb'>$titleInputCheckNb</label>
			</p>";

		// Privat
		$idShowpb = $this->get_field_id('show_pb');
		$nameInputCheck = $this->get_field_name('show_pb');
		$titleInputCheck = __('Приват банк', 'ukraine_currency');
		$checkedpb = checked( $show_pb, true, false );

		echo "<p>
				<input id='$idShowpb' class='checkbox' type='checkbox' $checkedpb name='$nameInputCheck'>
				<label for='$idShowpb'>$titleInputCheck</label>
			</p>";

	}
}
