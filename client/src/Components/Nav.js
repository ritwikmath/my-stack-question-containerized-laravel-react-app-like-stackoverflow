import React from 'react'
import { Link } from "react-router-dom";

export default function Nav() {
  return (
    <div>
        <div className="logo">MyStackAssignment</div>
        <nav>
            <menu>
                <li><Link to="/">Home</Link></li>
                <li><Link to="/questions">Questions</Link></li>
            </menu>
        </nav>
    </div>
  )
}
