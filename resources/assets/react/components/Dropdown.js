import React from 'react';

class Dropdown extends React.Component {
    constructor() {
        super();

        this.state = {
            showMenu: false,
        };

        this.closeMenu = this.closeMenu.bind(this);
    }

    showMenu(event) {
        event.preventDefault();

        this.setState({ showMenu: true }, () => {
            document.addEventListener('click', this.closeMenu);
        });
    }

    closeMenu(event) {

        if (!this.dropdownMenu.contains(event.target)) {
            this.setState({ showMenu: false }, () => {
                document.removeEventListener('click', this.closeMenu);
            });  
        }
    }

    render() {
        return (
            <div>
                <button className="nav-link admin dropdown-toggle" onClick={ (e) => this.showMenu(e) }>
                    { this.props.title }
                </button>
                { this.state.showMenu
                    ? (
                        <div
                            className="dropdown-menu d-block"
                            ref={(element) => {
                                this.dropdownMenu = element;
                            }}
                            >
                            { this.props.children }
                        </div>
                    )
                    : (
                        null
                    )
                }
            </div>
        );
    }
}

export default Dropdown;
