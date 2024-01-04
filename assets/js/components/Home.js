// ./assets/js/components/Home.js

import React from 'react';
import {NavLink, Route, Switch} from 'react-router-dom';
import ExchangeRatesPage from "./exchageRates/ExchageRatesPage";
import SetupCheck from "./SetupCheck";

export default function Home() {

    return (
        <div>
            <nav className="navbar navbar-expand-lg navbar-dark bg-dark">
                <NavLink className={"navbar-brand"} to={"/"}> Telemedi Zadanko </NavLink>
                <div id="navbarText">
                    <ul className="navbar-nav mr-auto">
                        <li className="nav-item">
                            <NavLink
                                to={"/exchange-rates"}
                                className={({isActive}) => (isActive ? 'nav-link active' : 'nav-link')}
                            >
                                Exchange rates
                            </NavLink>
                        </li>
                    </ul>
                </div>
            </nav>

            <Switch>
                {/*<Redirect exact from="/" to="/setup-check" />*/}
                <Route exact path="/" component={SetupCheck}/>
                <Route path="/exchange-rates/:chosenDate?" component={ExchangeRatesPage}/>
            </Switch>
        </div>
    )
}
