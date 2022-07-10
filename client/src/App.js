import React, { Component }  from 'react';
import { Routes, Route, Link } from "react-router-dom";
import './App.css';
import Home from './Components/Home';
import Nav from './Components/Nav';
import Note from './Components/Note';
import QuestionDetail from './Components/QuestionDetail';
import QuestionForm from './Components/QuestionForm';
import Questions from './Components/Questions';

function App() {
  return (
    <div className="App">
      <Nav />
      <Routes>
        <Route path='/' element={<Home />} />
        <Route path='questions' element={<Questions />} />
        <Route path='questions/:id' element={<QuestionDetail />} />
        <Route path='questions/add-new' element={<QuestionForm />} />
        <Route path='notes/:id' element={<Note />} />
      </Routes>
    </div>
  );
}

export default App;
