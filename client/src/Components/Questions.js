import React from 'react'
import { Link } from "react-router-dom";

export default function Questions() {
  return (
    <div>
        <h1>All Questions</h1>
        <Link to="add-new">Ask a Question</Link>
        <ul>
            <li><Link to="1">What is database?</Link></li>
            <li><Link to="2">What is BlockChain?</Link></li>
            <li><Link to="3">What is Dganjo?</Link></li>
        </ul>
    </div>
  )
}
