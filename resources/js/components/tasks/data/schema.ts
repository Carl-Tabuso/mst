import {  z } from 'zod'

export const taskSchema = z.object({
  id: z.string(),
  frontliner: z.string(),
  reason: z.string(),
  request_status: z. string(),
  date: z.date(),
})

export type Task = z.infer<typeof taskSchema>
