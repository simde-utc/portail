import React from 'react';
import { Link } from 'react-router-dom';
import AspectRatio from 'react-aspect-ratio';

export default class Block extends React.Component {
	render() {
		var style = this.props.style || {}
		style.cursor = 'pointer'

		return (
			<div className={ "m-2 p-2 " + this.props.class }
				style={ this.props.style } 
				onClick={ this.props.onClick }
			>
				<AspectRatio ratio="1">
					<img src={ this.props.image } style={{ width: '100%' }} />
				</AspectRatio>
				<span>{ this.props.text }</span>
			</div>
		);
	}
}
