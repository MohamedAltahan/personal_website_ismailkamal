@include('site.partials.project-grid', ['projects' => $projects, 'offset' => $offset])
<span data-next="{{ $projects->nextPageUrl() }}"></span>
