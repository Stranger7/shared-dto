export type Page = {
  // from 1 to page count
  pageNumber?: number;

  perPage?: number;

  sortFields?: string[];

  sortDirections?: string[];
}
