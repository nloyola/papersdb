#!/usr/bin/perl -w

#------------------------------------------------------------------------------
#
# Name: $Id: report.pl,v 1.18 2007/03/20 18:14:47 aicmltec Exp $
#
# See $USAGE.
#
#------------------------------------------------------------------------------

use strict;
use File::Basename;
use Getopt::Long;
use DBI;
use Data::Dumper;;

my $SCRIPTNAME = basename ($0);

my $USAGE = <<USAGE_END;
Usage: $SCRIPTNAME [options]

  Queries the AICML Papers Database and gathers publication statistics for the
  centre's principal investigators, post doctoral fellows and students.

USAGE_END

my @tier1venues = qw(AIJ AAAI IJCAI ICML NIPS JAIR MLJ NAR JMLR UAI CCR);

# Bioinformatics

my %years = (0 => ['2002-09-01', '2003-08-31'],
             1 => ['2003-09-01', '2004-08-31'],
             2 => ['2004-09-01', '2006-03-31'],
             3 => ['2006-04-01', '2007-03-31']);

my @pi_authors = ('Szepesvari, C',
                  'Schuurmans, D',
                  'Schaeffer, J',
                  'Bowling, M',
                  'Goebel, R',
                  'Sutton, R',
                  'Holte, R',
                  'Greiner, R');

my @pdf_authors = ('Botea, A',
                   'Brown, M',
                   'Caetano, T',
                   'Cheng, L',
                   'Engel, Y',
                   'Ghavamzadeh, M',
                   'Kirshner, S',
                   'Li, Y',
                   'Ludvig, E',
                   'Madani, O',
                   'Price, B',
                   'Ringlstetter, C',
                   'Southey, F',
                   'Wang, S',
                   'Zheng, T',
                   'Zinkevich, M'
               );

my @student_authors = ('Antonie, M',
                       'Asgarian, N',
                       'Bard, N',
                       'Billings, D',
                       'Botea, A',
                       'Chen, J',
                       'Coulthard, E',
                       'Davison, K',
                       'Dwyer, K',
                       'Farahmand, A',
                       'Fraser, B',
                       'Geramifard, A',
                       'Ghodsi, A',
                       'Guo, Y',
                       'Guo, Z',
                       'Heydari, M',
                       'Hlynka, M',
                       'Hoehn, B',
                       'Huang, J',
                       'Jiao, F',
                       'Johanson, M',
                       'Joyce, B',
                       'Kaboli, A',
                       'Kan, M',
                       'Kapoor, A',
                       'Koop, A',
                       'Lee, C',
                       'Lee, M',
                       'Levner, I',
                       'Li, L',
                       'Lizotte, D',
                       'Lu, Z',
                       'McCracken, P',
                       'Milstein, A',
                       'Morris, M',
                       'Neufeld, J',
                       'Newton, J',
                       'Niu, Y',
                       'Paduraru, C',
                       'Poulin, B',
                       'Rafols, E',
                       'Schauenberg, T',
                       'Schmidt, M',
                       'Silver, D',
                       'Singh, A',
                       'Tanner, B',
                       'Wang, P',
                       'Wang, Q',
                       'Wang, T',
                       'Wang, Y',
                       'White, A',
                       'Wilkinson, D',
                       'Wu, J',
                       'Wu, X',
                       'Xiao, G',
                       'Xu, L',
                       'Zhang, Q',
                       'Zheng, T',
                       'Zhu, T');

my @staff_authors = ('Arthur, R',
                     'Asgarian, N',
                     'Baich, T',
                     'Block, D',
                     'Coghlan, B',
                     'Coulthard, E',
                     'Coulthard, E',
                     'Dacyk, V',
                     'DeMarco, M',
                     'Duguid, L',
                     'Eisner, R',
                     'Farhangfar, A',
                     'Flatt, A',
                     'Fraser, S',
                     'Grajkowski, J',
                     'Harrison, E',
                     'Hiew, A',
                     'Hoehn, B',
                     'Homaeian, L',
                     'Huntley, D',
                     'Jewell, K',
                     'Koop, A',
                     'Larson, B',
                     'Loh, W',
                     'Loyola, N',
                     'Ma, G',
                     'McMillan, K',
                     'Melanson, A',
                     'Morris, M',
                     'Neufeld, J',
                     'Newton, J',
                     'Nicotra, L',
                     'Pareek, P',
                     'Parker, D',
                     'Paulsen, J',
                     'Poulin, B',
                     'Radkie, M',
                     'Roberts, J',
                     'Shergill, A',
                     'Smith, C',
                     'Sokolsky, M',
                     'Stephure, M',
                     'Thorne, W',
                     'Trommelen, M',
                     'Upright, C',
                     'Vicentijevic, M',
                     'Vincent, S',
                     'Walsh, S',
                     'White, T',
                     'Woloschuk, D',
                     'Young, A',
                     'Zheng, T',
                     'Zhu, T');

my $nonTier1CategoryCriteria
    = ' (category.category LIKE "%In Conference%" '
      . 'OR category.category LIKE "%In Journal%" '
      . 'OR category.category LIKE "%In Workshop%" '
      . 'OR category.category LIKE "%In Book%") ';

my $dbh = DBI->connect('DBI:mysql:pubDB;host=kingman.cs.ualberta.ca', 'papersdb', '')
    || die "Could not connect to database: $DBI::errstr";

sub getPub {
    my $pub_id = shift;
    my $statement;

    $statement = 'SELECT publication.pub_id, publication.title, '
        . 'publication.published, category.category '
        . 'FROM publication, category, pub_cat WHERE '
        . ' category.cat_id=pub_cat.cat_id '
        . 'AND publication.pub_id=pub_cat.pub_id '
        . 'AND publication.pub_id="' . $pub_id . '"';

    #print $statement . "\n";

    my %rv = %{ $dbh->selectall_hashref($statement, 'pub_id') };

    $statement = 'SELECT author.author_id, author.name FROM pub_author, author '
        . 'WHERE author.author_id=pub_author.author_id '
        . 'AND pub_author.pub_id="' . $pub_id . '"';

    #print $statement . "\n";

    my %rv2 = %{ $dbh->selectall_hashref($statement, 'author_id') };

    foreach my $author_id (sort keys %rv2) {
        push (@{ $rv{$pub_id}{'authors'} }, $rv2{$author_id}{'name'});
    }

    return %rv;
}

sub getPubsForPeriod {
    my $startdate = shift;
    my $enddate = shift;
    my $statement;

    $statement = 'SELECT DISTINCT publication.pub_id '
        . 'FROM publication, category, pub_cat WHERE '
        . ' category.cat_id=pub_cat.cat_id '
        . 'AND publication.pub_id=pub_cat.pub_id '
        . 'AND ' . $nonTier1CategoryCriteria
        . 'AND publication.published BETWEEN \''
        . $startdate . '\' AND \'' . $enddate . '\'';

    #print $statement . "\n";

    my %rv = %{ $dbh->selectall_hashref($statement, 'pub_id') };
    return %rv;
}

sub getNumPubsForPeriod {
    my $startdate = shift;
    my $enddate = shift;
    my $statement;

    $statement = 'SELECT DISTINCT publication.pub_id, publication.title '
        . 'FROM publication, category, pub_cat WHERE '
        . ' category.cat_id=pub_cat.cat_id '
        . 'AND publication.pub_id=pub_cat.pub_id '
        . 'AND ' . $nonTier1CategoryCriteria
        . 'AND publication.published BETWEEN \''
        . $startdate . '\' AND \'' . $enddate . '\'';

    #print $statement . "\n";

    my %rv = %{ $dbh->selectall_hashref($statement, 'pub_id') };
    return scalar(keys %rv);
}

sub getPubs {
    my $authors = shift;
    my $startdate = shift;
    my $enddate = shift;
    my $tier1only = shift;
    my $statement;

    if ((defined $tier1only) && ($tier1only eq "Y")) {
        $statement = 'SELECT DISTINCT publication.pub_id, '
            . 'publication.title FROM '
            . 'publication, author, pub_author, venue WHERE '
            . 'venue.title IN ('
            . join(', ', map { $dbh->quote($_) } @tier1venues) . ') '
            . 'AND publication.venue_id=venue.venue_id ';
    }
    elsif ((defined $tier1only) && ($tier1only eq "N")) {
        $statement = 'SELECT DISTINCT publication.pub_id, '
            . 'publication.title FROM '
            . 'publication, author, pub_author, venue, category, pub_cat '
            . 'WHERE venue.title NOT IN ('
            . join(', ', map { $dbh->quote($_) } @tier1venues) . ') '
            . 'AND ' . $nonTier1CategoryCriteria
            . 'AND category.cat_id=pub_cat.cat_id '
            . 'AND publication.pub_id=pub_cat.pub_id '
            . 'AND publication.venue_id=venue.venue_id ';
    }
    elsif (!defined $tier1only) {
        $statement = 'SELECT DISTINCT publication.pub_id, '
            . 'publication.title FROM '
            . 'publication, author, pub_author, category, pub_cat WHERE '
            . $nonTier1CategoryCriteria
            . 'AND category.cat_id=pub_cat.cat_id '
            . 'AND publication.pub_id=pub_cat.pub_id ';
    }

    if ((defined @$authors) && (@$authors > 0)) {
        my @list;
        foreach my $author (@$authors) {
            push(@list, 'author.name LIKE "' . $author . '%"');
        }
        $statement .= 'AND (' . join(' OR ', @list) . ') ';
    }

    $statement .= 'AND publication.pub_id=pub_author.pub_id '
        . 'AND publication.keywords LIKE "%machine learning%" '
        . 'AND author.author_id=pub_author.author_id '
        . 'AND publication.published BETWEEN \''
        . $startdate . '\' AND \'' . $enddate . '\'';

    #print $statement . "\n";

    my %rv = %{ $dbh->selectall_hashref($statement, 'pub_id') };

    # if requested non Tier 1 publications, then we must include the
    # publications with NULL venue_id
    if ((defined $tier1only) && ($tier1only eq "N")) {
        $statement = 'SELECT publication.pub_id, publication.title FROM '
            . 'publication, author, pub_author, category, pub_cat WHERE ';

        if ((defined @$authors) && ($#$authors >= 0)) {
            my @list;
            foreach my $author (@$authors) {
                push(@list, 'author.name LIKE "%' . $author . '%"');
            }
            $statement .= '(' . join(' OR ', @list) . ') ';
        }

        $statement .= 'AND ' . $nonTier1CategoryCriteria
            .  'AND publication.venue_id is NULL '
            . 'AND publication.pub_id=pub_author.pub_id '
            . 'AND author.author_id=pub_author.author_id '
            . 'AND category.cat_id=pub_cat.cat_id '
            . 'AND publication.pub_id=pub_cat.pub_id '
            . 'AND publication.published BETWEEN \''
            . $startdate . '\' AND \'' . $enddate . '\'';

        #print $statement . "\n";

        my %rv2 = %{ $dbh->selectall_hashref($statement, 'pub_id') };
        %rv = (%rv, %rv2);
    }

    return %rv;
}

sub getPubAuthors {
    my $pub_id = shift;
    my $authors = shift;

    my $statement = 'SELECT author.name FROM '
        . 'publication, author, pub_author WHERE '
        . 'publication.pub_id=' . $pub_id . ' AND '
        . 'publication.pub_id=pub_author.pub_id AND '
        . 'author.author_id=pub_author.author_id AND ';

    if ((defined @$authors) && (scalar @$authors >= 0)) {
        my @list;
        foreach my $author (@$authors) {
            push(@list, 'author.name LIKE "%' . $author . '%"');
        }
        $statement .= '(' . join(' OR ', @list) . ') ';
    }
    $statement .= 'ORDER BY author.name';

    #print $statement . "\n";

    my $rv = $dbh->selectall_hashref($statement, 'name');
    return %$rv;
}

sub piReport {
    my %pubs;
    my %authors;
    my %author_pubs;

    foreach my $year (sort keys %years) {
        foreach my $t1 (qw(Y N)) {
            %pubs = getPubs(\@pi_authors, $years{$year}[0], $years{$year}[1], $t1);

            foreach my $pub_id (sort keys %pubs) {
                my %pub_authors = getPubAuthors($pub_id, \@pi_authors);

                my $num_authors = scalar(keys %pub_authors);
                my $authors = join(':', keys %pub_authors);

                $author_pubs{$year}{$t1}{$authors}{'num_authors'} = $num_authors;
                push(@{ $author_pubs{$year}{$t1}{$authors}{'pubs'} }, $pub_id);

                push(@{ $authors{$authors}{$t1} }, $pub_id);
                if ($num_authors > 1) {
                    push(@{ $authors{'multiple'}{$t1} }, $pub_id);
                }
            }
        }
    }

    my %totals;

    print "Tier-1 Venues: " . join(", ", @tier1venues) . "\n\n"
        . "TIME PERIOD;T1;AUTHORS;NUM AUTHORS;NUM PUBS;PUB IDS\n";

    foreach my $year (sort keys %author_pubs) {
        foreach my $t1 (sort keys %{ $author_pubs{$year} }) {
            $totals{$year}{$t1} = 0;
            foreach my $authors (sort keys %{ $author_pubs{$year}{$t1} }) {
                printf "%s - %s;%s;%s;%d;%d;", $years{$year}[0], $years{$year}[1],
                    $t1, $authors,
                        $author_pubs{$year}{$t1}{$authors}{'num_authors'},
                            scalar @{ $author_pubs{$year}{$t1}{$authors}{'pubs'} };
                print "\""
                    . join(', ', sort @{ $author_pubs{$year}{$t1}{$authors}{'pubs'} })
                        . "\"\n";

                $totals{$year}{$t1}
                    += scalar @{ $author_pubs{$year}{$t1}{$authors}{'pubs'} }
                }
        }
    }

    print "\n\nTIME PERIOD;T1;NUM PUBS\n";
    foreach my $year (sort keys %author_pubs) {
        foreach my $t1 (sort keys %{ $author_pubs{$year} }) {
            printf "%s - %s;%s;%d\n", $years{$year}[0], $years{$year}[1],
                $t1, $totals{$year}{$t1};
        }
    }

    print "\n\nAUTHOR(S);T1;NUM PUBS\n";
    foreach my $authors (sort keys %authors) {
        foreach my $t1 (sort keys %{ $authors{$authors} }) {
            printf "%s;%s;%d\n", $authors, $t1, scalar(@{ $authors{$authors}{$t1} });
        }
    }
}

sub pdfStudentReport {
    my %pubs;
    my %authors;
    my %author_pubs;
    my @pdf_students_staff = (@pdf_authors, @student_authors, @staff_authors);
    my @pi_pdf_students_staff = (@pi_authors, @pdf_authors, @student_authors, @staff_authors);

    foreach my $year (sort keys %years) {
        my %pubs = getPubsForPeriod($years{$year}[0], $years{$year}[1]);

        foreach my $pub_id (sort keys %pubs) {
            my %pub_authors = getPubAuthors($pub_id, \@pdf_students_staff);

            # does this publication have an author that is an AICML PDF or student?
            if (scalar(keys %pub_authors) == 0) {
                #my %pub = getPub($pub_id);
                #print join(': ', @{ $pub{$pub_id}{'authors'} })   . ". "
                #    . $pub{$pub_id}{'title'} . ". "
                #    . $pub{$pub_id}{'category'} . ". "
                #    . $pub{$pub_id}{'published'}
                #    . ".\n";
                next;
            }

            # now get all authors for this pub that are PI's, PDF's and students
            %pub_authors = getPubAuthors($pub_id, \@pi_pdf_students_staff);

            my $num_authors = scalar(keys %pub_authors);
            my $authors = join(':', keys %pub_authors);

            $author_pubs{$year}{$authors}{'num_authors'} = $num_authors;
            push(@{ $author_pubs{$year}{$authors}{'pubs'} }, $pub_id);

            push(@{ $authors{$authors} }, $pub_id);
            if ($num_authors > 1) {
                push(@{ $authors{'multiple'} }, $pub_id);
            }
        }
        print "\n";
    }

    my %totals;

    print "\n\nPublications by PDFs and Students\n\n"
        . "TIME PERIOD;AUTHORS;NUM AUTHORS;NUM PUBS;PUB IDS\n";

    foreach my $year (sort keys %author_pubs) {
        $totals{$year} = 0;
        foreach my $authors (sort keys %{ $author_pubs{$year} }) {
            printf "%s - %s;%s;%d;%d;", $years{$year}[0], $years{$year}[1],
                $authors, $author_pubs{$year}{$authors}{'num_authors'},
                scalar @{ $author_pubs{$year}{$authors}{'pubs'} };
            print "\""
                . join(', ', sort @{ $author_pubs{$year}{$authors}{'pubs'} })
                . "\"\n";

            $totals{$year}
                += scalar @{ $author_pubs{$year}{$authors}{'pubs'} }
            }
    }

    print "\n\nTIME PERIOD;NUM PUBS FOR PDF AND STUDENT;TOT PUBS;\"%\"\n";
    foreach my $year (sort keys %author_pubs) {
        my $totPubs = getNumPubsForPeriod($years{$year}[0], $years{$year}[1]);

        printf "%s - %s;%d;%d;%f\n", $years{$year}[0], $years{$year}[1],
            $totals{$year}, $totPubs, ($totals{$year} * 100 / $totPubs);
    }

    print "\n\nAUTHOR(S);NUM PUBS\n";
    foreach my $authors (sort keys %authors) {
        printf "%s;%d\n", $authors, scalar(@{ $authors{$authors} });
    }
}

#if (!GetOptions ('noposters' => \$noposters)) {
#    die "ERROR: bad options in command line\n";
#}


piReport();

pdfStudentReport();

print "\n\nPDFs or Students not in Database\n";

my @pdf_students = (@pdf_authors, @student_authors);
foreach my $author (@pdf_students) {
    my $statement = 'SELECT author_id, name FROM author WHERE name like "%'
        . $author . '%"';
    my %rv = %{ $dbh->selectall_hashref($statement, 'author_id') };
    if (scalar(keys %rv) == 0) {
        print $author . "\n";
    }
}

$dbh->disconnect();

1;
