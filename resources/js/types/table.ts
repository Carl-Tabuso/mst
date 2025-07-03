export interface TableColumn {
  id: string
  header: string
  accessor?: string
  cell?: (row: any) => any
}
