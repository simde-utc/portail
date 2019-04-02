import React from 'react';
import {
	Modal,
	ModalBody,
	ModalHeader,
	ModalFooter,
	Form,
	FormGroup,
	Button,
	Label,
	Input,
} from 'reactstrap';
import { connect } from 'react-redux';

import SimpleMDE from 'simplemde';
import Editor from 'react-simplemde-editor';
import Select from 'react-select';
import { map } from 'lodash';

import actions from '../../redux/actions';

import 'easymde/dist/easymde.min.css';

const options = {
	spellChecker: false,
	toolbar: [
		{
			name: 'bold',
			action: SimpleMDE.toggleBold,
			className: 'fa fa-fw fa-bold',
			title: 'Gras',
		},
		{
			name: 'italic',
			action: SimpleMDE.toggleItalic,
			className: 'fa fa-fw fa-italic',
			title: 'Italique',
		},
		{
			name: 'heading',
			action: SimpleMDE.toggleHeadingSmaller,
			className: 'fa fa-fw fa-heading',
			title: 'Titre',
		},
		{
			name: 'quote',
			action: SimpleMDE.toggleBlockquote,
			className: 'fa fa-fw fa-quote-left',
			title: 'Citation',
		},
		{
			name: 'unordered-list',
			action: SimpleMDE.toggleUnorderedList,
			className: 'fa fa-fw fa-list-ul',
			title: 'Liste non-ordonnée',
		},
		{
			name: 'ordered-list',
			action: SimpleMDE.toggleOrderedList,
			className: 'fa fa-fw fa-list-ol',
			title: 'Liste ordonnée',
		},
		{
			name: 'link',
			action: SimpleMDE.drawLink,
			className: 'fa fa-fw fa-link',
			title: 'Insérer un lien',
		},
		{
			name: 'image',
			action: SimpleMDE.drawImage,
			className: 'fa fa-fw fa-image',
			title: 'Insérer une image',
		},
		{
			name: 'table',
			action: SimpleMDE.drawTable,
			className: 'fa fa-fw fa-table',
			title: 'Insérer un tableau',
		},
	],
};

@connect(store => ({
	visibilities: store.getData('visibilities'),
}))
class ArticleForm extends React.Component {
	static mapSelectionOptions(options) {
		return map(options, ({ id, name }) => ({
			value: id,
			label: name,
		}));
	}

	constructor(props) {
		super(props);

		this.state = {
			title: '',
			description: '',
			content: '',
		};

		if (props.visibilities.length === 0) {
			props.dispatch(actions.visibilities.get());
		}
	}

	getEvents(events) {
		const { eventFilter } = this.state;

		return ArticleForm.mapSelectionOptions(
			events.filter(eventToFilter => {
				return eventToFilter.name.indexOf(eventFilter) >= 0;
			})
		);
	}

	handleVisibilityChange({ value }) {
		this.setState({ visibility_id: value });
	}

	handleSearchEvent(value) {
		this.setState({ eventFilter: value });
	}

	handleEventChange(value) {
		this.setState({ event_id: value.value });
	}

	handleEditorChange(value) {
		this.setState({ content: value });
	}

	handleChange(e) {
		e.persist();

		this.setState({ [e.target.name]: e.target.value });
	}

	handleSubmit(e) {
		const { title, description, content, visibility_id, event_id } = this.state;
		const { post } = this.props;
		e.preventDefault();

		post({
			title,
			description,
			content,
			visibility_id,
			event_id,
		});
	}

	render() {
		const { opened, visibilities, closeModal } = this.props;
		const { title, description } = this.state;

		return (
			<Modal className="modal-dialog-extended" isOpen={opened}>
				<Form onSubmit={this.handleSubmit.bind(this)}>
					<ModalHeader>Créer un article</ModalHeader>
					<ModalBody>
						<FormGroup>
							<Label for="access_id">Titre *</Label>
							<Input
								type="text"
								className="form-control"
								id="title"
								name="title"
								value={title}
								onChange={e => this.handleChange(e)}
								placeholder="Titre de l'article"
								required
							/>
						</FormGroup>

						<FormGroup>
							<Label for="description">Contenu *</Label>
							<Editor
								onChange={e => this.handleEditorChange(e)}
								id="corps"
								options={options}
								required
							/>
						</FormGroup>

						<FormGroup>
							<Label for="description">Description</Label>
							<Input
								type="textarea"
								className="form-control"
								id="description"
								name="description"
								rows="3"
								value={description}
								onChange={e => this.handleChange(e)}
								placeholder="Courte description de l'article"
							/>
						</FormGroup>

						<FormGroup>
							<Label for="description">Visibilité *</Label>
							<Select
								onChange={this.handleVisibilityChange.bind(this)}
								name="visibility_id"
								placeholder="Visibilité de l'article"
								options={ArticleForm.mapSelectionOptions(visibilities)}
							/>
						</FormGroup>
					</ModalBody>
					<ModalFooter>
						<Button outline className="font-weight-bold" onClick={() => closeModal()}>
							Annuler
						</Button>
						<Button type="submit" className="font-weight-bold" outline color="primary">
							Publier l'article
						</Button>
					</ModalFooter>
				</Form>
			</Modal>
		);
	}
}

// <div className="form-group">
//     <AsyncSelect
//         onChange={this.handleEventChange.bind(this)}
//         name="event_id"
//         placeholder="Evènement attaché"
//         isSearchable
//         onInputChange={this.handleSearchEvent.bind(this)}
//         options={this.getEvents(events)}
//     />
// </div>

export default ArticleForm;
