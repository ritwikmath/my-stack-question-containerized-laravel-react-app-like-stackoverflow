import React from 'react'
import { useForm } from "react-hook-form";

export default function QuestionForm() {
  const { register, handleSubmit, watch, formState: { errors } } = useForm();
  const onSubmit = data => console.log(data);

  return (
    <div>
        <h1>Add New Question</h1>
        <form onSubmit={handleSubmit(onSubmit)}>
            <input {...register("title", { required: true })} />
            {errors.title?.type === 'required' && "Title is required"}

            <input type="submit" />
        </form>
    </div>
  )
}
