import React from "react";

export default function Spinner(){
    return (
        <div className={'text-center'} role={"status"} aria-roledescription={"status"}>
            <span className="fa fa-spin fa-spinner fa-4x"></span>
        </div>
    );
}