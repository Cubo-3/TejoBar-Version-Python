from collections.abc import Iterator
from typing import Any

from whitenoise.storage import CompressedStaticFilesStorage


class SequentialCompressedStaticFilesStorage(CompressedStaticFilesStorage):
    """Compressed static files without parallel threads (avoids collectstatic races)."""

    def post_process(
        self, paths: dict[str, Any], dry_run: bool = False, **options: Any
    ) -> Iterator[tuple[str, str, bool]]:
        if dry_run:
            return

        from django.conf import settings
        from whitenoise.compress import Compressor

        extensions = getattr(settings, "WHITENOISE_SKIP_COMPRESS_EXTENSIONS", None)
        self.compressor = self.create_compressor(extensions=extensions, quiet=True)

        for path in paths:
            if not self.compressor.should_compress(path):
                continue
            full_path = self.path(path)
            prefix_len = len(full_path) - len(path)
            for compressed_path in self.compressor.compress(full_path):
                compressed_name = compressed_path[prefix_len:]
                yield path, compressed_name, True
