import React from 'react';

class Modal extends React.Component { 
	render() {
		// onClick={ this.props.onClose } in dimmer
		return (
			<div className={ "modal fade" + (this.props.show ? ' show' : '') } tabIndex="-1" role="dialog">
				<div className="modal-dialog modal-lg">
					<div className="modal-content">
						<div className="modal-header">
							{ this.props.title }
							<button type="button" className="close" onClick={this.props.onClose}>
								&times;</button>
						</div>
						<div className="modal-body p-3">
							{ this.props.children }
						</div>
					</div>
				</div>
			</div>
		);
	}
};

export default Modal;
