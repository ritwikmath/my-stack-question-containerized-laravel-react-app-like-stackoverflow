import React, { useEffect, useState } from "react";
import MDEditor from '@uiw/react-md-editor';

export default function Note() {
    const [value, setValue] = React.useState("**Hello world!!!**");
    const [viewMode, setViewMode] = useState(true) 
    const renderView = () => {
        if (viewMode) 
            return  <MDEditor.Markdown source={value} style={{ whiteSpace: 'pre-wrap' }} />
        else
            return <MDEditor value={value} onChange={setValue} />
    }

    useEffect(() => {
        const persistedData = localStorage.getItem('notes')
        const persistedViewMode = localStorage.getItem('view')
        if (persistedData) setValue(JSON.parse(persistedData))
        if (persistedViewMode) setViewMode(JSON.parse(persistedViewMode))
    }, []);

    useEffect(() => {
        if (value != "**Hello world!!!**") localStorage.setItem('notes', JSON.stringify(value))
    }, [value]);

    const updateViewMode = () => {
        setViewMode(!viewMode)
        localStorage.setItem('view', JSON.stringify(!viewMode))
    }

    const save = () => {
        console.log(JSON.stringify(value))
    }
    
    return (
      <div className="container">
        <button onClick={updateViewMode}>Switch View</button>
        {renderView()}
        <button onClick={save}>Save</button>        
      </div>
    );
}
