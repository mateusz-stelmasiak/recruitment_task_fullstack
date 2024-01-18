/**
 * @jest-environment jsdom
 */
import React from "react";
import {render, screen, test, expect, fireEvent} from "./test-utils";
import Home from "../components/Home";
import "@testing-library/jest-dom";

test('renders link to exchange rates', () => {
    render(<Home/>);
    const linkElement = screen.getByRole('link', {name: /Exchange rates/i});
    expect(linkElement).toHaveTextContent(/Exchange rates/i);
    expect(linkElement).toHaveAttribute('href', '/exchange-rates');
})

// test('link to exchange rates takes us to exchange rates page', async () => {
//     render(<Home/>);
//     const linkElement = screen.getByText(/Exchange rates/i);
//     fireEvent.click(linkElement);
//     await waitFor(()=>{
//         const titleValue = screen.getByRole('heading', {level: 1});
//         expect(titleValue).toBeInTheDocument();
//         expect(titleValue).toHaveTextContent(/Exchange rates/i);
//     })
// });

