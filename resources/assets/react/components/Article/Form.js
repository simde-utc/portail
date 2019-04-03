/**
 * Formulaire de création d'article.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

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
			visibility_id: null,
			visibility_name: null,
			title: '',
			description: '',
			content: '',
		};

		if (props.visibilities.length === 0) {
			props.dispatch(actions.visibilities.get());
		} else {
			this.setDefaultVisibility(props.visibilities);
		}
	}

	componentDidUpdate(lastProps) {
		const { visibilities } = this.props;

		if (lastProps.visibilities.length !== visibilities.length) {
			this.setDefaultVisibility(visibilities);
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

	setDefaultVisibility(visibilities) {
		const defaultVisibility = visibilities.find(visibility => visibility.type === 'public');

		this.setState({
			visibility_id: defaultVisibility.id,
			visibility_name: defaultVisibility.name,
		});
	}

	cleanInputs() {
		const { visibilities } = this.props;

		this.setState({
			title: '',
			description: '',
			content: '',
		});

		this.setDefaultVisibility(visibilities);
	}

	handleSubmit(e) {
		const { post } = this.props;
		const { title, description, content, visibility_id, event_id } = this.state;
		e.preventDefault();

		post({
			title,
			description,
			content,
			visibility_id,
			event_id,
		}).then(() => {
			this.cleanInputs();
		});
	}

	handleSearchEvent(value) {
		this.setState({ eventFilter: value });
	}

	handleEventChange(value) {
		this.setState({ event_id: value.value });
	}

	handleContentChange(content) {
		this.setState({ content });
	}

	handleDescriptionChange(description) {
		this.setState({ description });
	}

	handleChange(e) {
		e.persist();

		this.setState({ [e.target.name]: e.target.value });
	}

	handleVisibilityChange({ value, label }) {
		this.setState({ visibility_id: value, visibility_name: label });
	}

	render() {
		const { opened, visibilities, closeModal } = this.props;
		const { title, description, content, visibility_id, visibility_name } = this.state;

		return (
			<Modal className="modal-dialog-extended" isOpen={opened}>
				<Form onSubmit={this.handleSubmit.bind(this)}>
					<ModalHeader toggle={closeModal.bind(this)}>Créer un article</ModalHeader>
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
							<Label for="corps">Contenu *</Label>
							<Editor
								onChange={e => this.handleContentChange(e)}
								id="corps"
								options={options}
								value={content}
								required
							/>
						</FormGroup>

						<FormGroup>
							<Label for="description">Description (courte description de l'article)</Label>
							<Editor
								onChange={e => this.handleDescriptionChange(e)}
								id="description"
								options={options}
								value={description}
								required
							/>
						</FormGroup>

						<FormGroup>
							<Label for="visibility_id">Visibilité *</Label>
							<Select
								onChange={this.handleVisibilityChange.bind(this)}
								name="visibility_id"
								placeholder="Visibilité de l'article"
								options={ArticleForm.mapSelectionOptions(visibilities)}
								value={{ value: visibility_id, label: visibility_name }}
							/>
						</FormGroup>
					</ModalBody>
					<ModalFooter>
						<Button
							className="btn-reinit"
							outline
							color="danger"
							onClick={() => this.cleanInputs()}
						>
							Réinitialiser
						</Button>
						<Button outline onClick={() => closeModal()}>
							Annuler
						</Button>
						<Button type="submit" outline color="primary">
							Publier l'article
						</Button>
					</ModalFooter>
				</Form>
			</Modal>
		);
	}
}

export default ArticleForm;
