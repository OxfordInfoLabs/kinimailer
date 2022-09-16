import { TestBed } from '@angular/core/testing';

import { MailingProfileService } from './mailing-profile.service';

describe('MailingProfileService', () => {
  let service: MailingProfileService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(MailingProfileService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
